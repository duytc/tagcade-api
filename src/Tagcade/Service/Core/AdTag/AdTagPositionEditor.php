<?php

namespace Tagcade\Service\Core\AdTag;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\Core\PositionInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Service\TagLibrary\ChecksumValidatorInterface;
use Tagcade\Worker\Manager;

class AdTagPositionEditor implements AdTagPositionEditorInterface
{
    /**
     * @var ContainerInterface
     *
     * Using the container to avoid circular dependency injection.
     * Only inject directly this Replicator service to DomainManager, do not inject DomainManager in to this Replicator service. Use Container to get DomainManager!!!
     *
     * e.g: AdTagPositionEditor -> AdTagManager -> Replicator -> AdTagPositionEditor
     */
    private $container;

    /** @var EntityManagerInterface */
    private $em;

    /** @var ChecksumValidatorInterface */
    private $validator;

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @param ContainerInterface $container
     * @param EntityManagerInterface $em
     * @param Manager $manager
     */
    function __construct(ContainerInterface $container, EntityManagerInterface $em, Manager $manager)
    {
        $this->container = $container;
        $this->em = $em;
        $this->manager = $manager;
    }

    /**
     * @param ChecksumValidatorInterface $validator
     */
    public function setValidator(ChecksumValidatorInterface $validator) {
        $this->validator = $validator;
    }

    /**
     * @inheritdoc
     */
    private function getAdTagManager()
    {
        return $this->container->get('tagcade.domain_manager.ad_tag');
    }

    /**
     * @inheritdoc
     */
    private function getLibrarySlotAdTagManager()
    {
        return $this->container->get('tagcade.domain_manager.library_slot_tag');
    }

    /**
     * set WaterfallTag Position For AdNetwork And Sites (optional, one or array or null for all),
     * also, we support auto-Increase-Position(shift down) for all ad tags of other ad network
     *
     * @param AdNetworkInterface $adNetwork
     * @param int $position
     * @param null|SiteInterface|SiteInterface[] $sites optional
     * @param bool $autoIncreasePosition optional, true if need shift down
     * @return int
     */
    public function setAdTagPositionForAdNetworkAndSites(AdNetworkInterface $adNetwork, $position, $sites = null, $autoIncreasePosition = false)
    {
        if (!is_int($position) || $position < 1) {
            throw new InvalidArgumentException('expect positive integer for ad tag position');
        }

        if (null === $sites || (is_array($sites) && count($sites) < 1)) {
            return $this->setAdTagPositionForAdNetwork($adNetwork, $position,$autoIncreasePosition);
        }

        $filterSites = [];

        if(!is_array($sites)) {
            $filterSites[] = $sites;
        }
        else {
            $filterSites = array_filter($sites, function($site) {return $site instanceof SiteInterface;});
        }

        if (!current($filterSites) instanceof SiteInterface) {
            throw new InvalidArgumentException('Expect site interface');
        }

        $adTags = $this->getAdTagManager()->getAdTagsForAdNetworkAndSites($adNetwork, $filterSites);

        return $this->updatePosition($adTags, $position, $autoIncreasePosition, $notIncludedAdNetworks = [$adNetwork]);
    }

    /**
     * @param DisplayAdSlotInterface $adSlot
     * @param array $newAdTagOrderIds ordered array of array [[adtag1_pos_1, adtag2_pos_1], [adtag3_pos2]]
     * @return \Tagcade\Model\Core\AdTagInterface[]
     */
    public function setAdTagPositionForAdSlot(DisplayAdSlotInterface $adSlot, array $newAdTagOrderIds) {
        $adTags = $adSlot->getAdTags()->toArray();

        return $this->updatePositionForTags($adTags, $newAdTagOrderIds);
    }

    /**
     * @param LibraryDisplayAdSlotInterface $adSlot
     * @param array $newAdTagOrderIds ordered array of array [[adtag1_pos_1, adtag2_pos_1], [adtag3_pos2]]
     * @return \Tagcade\Model\Core\LibrarySlotTagInterface[]
     */
    public function setAdTagPositionForLibraryAdSlot(LibraryDisplayAdSlotInterface $adSlot, array $newAdTagOrderIds) {
        $adTags = $adSlot->getLibSlotTags()->toArray();

        return $this->updatePositionForTags($adTags, $newAdTagOrderIds);
    }

    /**
     * Update position of $adTags to new order list of $newAdTagOrderIds
     * @param PositionInterface[] $adTags
     * @param array $newAdTagOrderIds
     *
     * @return array
     */
    protected function updatePositionForTags(array $adTags, array $newAdTagOrderIds)
    {
        if (empty($adTags)) {
            return [];
        }

        $adTagMap = array();
        foreach ($adTags as $adTag) {
            /**
             * @var PositionInterface $adTag
             */
            $adTagMap[$adTag->getId()] = $adTag;
        }

        $pos = 1;
        $orderedAdTags = [];
        $processedAdTags = [];

        try {
            $this->em->getConnection()->beginTransaction();

            foreach ($newAdTagOrderIds as $adTagIds) {
                foreach ($adTagIds as $adTagId) { // group of same position tags
                    if (!array_key_exists($adTagId, $adTagMap)) {
                        throw new RuntimeException('One of ids not existed in ad tag list of current ad slot');
                    }

                    if (in_array((int)$adTagId, $processedAdTags)) {
                        throw new RuntimeException('There is duplication of ad tag');
                    }

                    $adTag = $adTagMap[$adTagId];
                    if ($pos != $adTag->getPosition()) {
                        $adTag->setPosition($pos);
                        //update to slot tag if this is a shared ad slot
                        $libAdSlot = $adTag instanceof AdTagInterface ? $adTag->getAdSlot()->getLibraryAdSlot() : $adTag->getContainer();
                        // update position for library slot tag
                        if ($adTag instanceof AdTagInterface) {
                            $librarySlotTag = $this->getLibrarySlotAdTagManager()->getByLibraryAdSlotAndRefId($libAdSlot, $adTag->getRefId());
                            if ($librarySlotTag instanceof LibrarySlotTagInterface) {
                                $librarySlotTag->setPosition($pos);
                            }
                        }
                        //update all referenced AdTags if they are shared ad slot library
                        $this->manager->updateAdTagPositionForLibSlot($libAdSlot->getId(), $adTag->getid(), $pos);
                    }

                    $processedAdTags[] = $adTag->getId();
                    $orderedAdTags[] = $adTag;
                }

                $pos ++;
            }

            $tag = current($adTags);
            $adSlots = $tag instanceof AdTagInterface ? $tag->getAdSlot()->getCoReferencedAdSlots() : $tag->getContainer()->getAdSlots();
            if($adSlots instanceof PersistentCollection) $adSlots = $adSlots->toArray();

            $this->em->flush();
            $this->validator->validateAllAdSlotsSynchronized($adSlots);

            $this->em->getConnection()->commit();


        } catch(\Exception $e) {
            $this->em->getConnection()->rollBack();
            throw new RuntimeException($e);
        }

        return $orderedAdTags;
    }

    /**
     * set WaterfallTag Position For AdNetwork (i.e for all sites),
     * also, we support auto-Increase-Position(shift down) for all ad tags of other ad network
     *
     * @param AdNetworkInterface $adNetwork
     * @param int $position
     * @param bool $autoIncreasePosition optional, true if need shift down
     * @return int number of ad tags get position updated
     */
    protected function setAdTagPositionForAdNetwork(AdNetworkInterface $adNetwork, $position, $autoIncreasePosition = false)
    {
        $adTags = $this->getAdTagManager()->getAdTagsForAdNetwork($adNetwork);

        return $this->updatePosition($adTags, $position, $autoIncreasePosition, $notIncludedAdNetworks = [$adNetwork]);
    }

    /**
     * update Position for ad tags,
     * also, we support auto-Increase-Position(shift down) for all ad tags of other ad networks not in the $notIncludedAdNetworks config
     *
     * @param array $adTags
     * @param int $position
     * @param bool $autoIncreasePosition
     * @param array $notIncludedAdNetworks optional, empty array for shift down all
     * @return int
     */
    protected function updatePosition(array $adTags, $position, $autoIncreasePosition = false, $notIncludedAdNetworks = [])
    {
        $allTagsToBeUpdated = $adTags;
        $allTagsToBeIncreasedPosition = [];

        foreach($adTags as $adTag) {
            /**
             * @var AdTagInterface $adTag
             */
            // update to slot tag if this is a shared ad slot
            $libAdSlot = $adTag->getAdSlot()->getLibraryAdSlot();

            // update position for library slot tag
            $librarySlotTag = $this->getLibrarySlotAdTagManager()->getByLibraryAdSlotAndRefId($libAdSlot, $adTag->getRefId());
            if ($librarySlotTag instanceof LibrarySlotTagInterface) {
                if (!in_array($librarySlotTag, $allTagsToBeUpdated)) {
                    $allTagsToBeUpdated[] = $librarySlotTag;
                }
            }

            if ($autoIncreasePosition) {
                $librarySlotTagToBeIncreasedPosition = $this->getLibrarySlotAdTagManager()->getByLibraryAdSlotAndDifferRefId($libAdSlot, $adTag->getRefId());

                if (is_array($librarySlotTagToBeIncreasedPosition)) {
                    $allTagsToBeIncreasedPosition = array_merge($allTagsToBeIncreasedPosition, $librarySlotTagToBeIncreasedPosition);
                }
            }

            // update all referenced AdTags if they are shared ad slot library
            $referencedTags = $this->getAdTagManager()->getAdTagsByLibraryAdSlotAndRefId($libAdSlot, $adTag->getRefId());
            if(!empty($referencedTags)) {
                array_walk(
                    $referencedTags,
                    function(AdTagInterface $t) use(&$allTagsToBeUpdated)
                    {
                        if (!in_array($t, $allTagsToBeUpdated)) {
                            $allTagsToBeUpdated[] = $t;
                        }
                    }
                );
            }

            if ($autoIncreasePosition) {
                $referencedTagsToBeIncreasedPosition = $this->getAdTagManager()->getAdTagsByLibraryAdSlotAndDifferRefId($libAdSlot, $adTag->getRefId());

                if (is_array($referencedTagsToBeIncreasedPosition)) {
                    $allTagsToBeIncreasedPosition = array_merge($allTagsToBeIncreasedPosition, $referencedTagsToBeIncreasedPosition);
                }
            }
        }

        $updateCount = 0;

        // sure unique to avoid duplicate increase position
        $allTagsToBeUpdated = array_unique($allTagsToBeUpdated);

        array_walk(
            $allTagsToBeUpdated,
            function(PositionInterface $adTag) use ($position, &$updateCount)
            {
                /**
                 * @var AdTagInterface $adTag
                 */
                if ($adTag instanceof AdTagInterface && !$adTag->getAdSlot() instanceof DisplayAdSlotInterface) {
                    return; // not updating position for other types of ad slot like native ad slot
                }

                if ($adTag instanceof LibrarySlotTagInterface && !$adTag->getContainer() instanceof LibraryDisplayAdSlotInterface) {
                    return; // not updating position for other types of ad slot like native ad slot
                }

                if ($adTag->getPosition() != $position ) {
                    $adTag->setPosition($position);
                    $updateCount ++;
                }
            }
        );

        // support autoIncreasePosition
        if ($autoIncreasePosition) {
            // mapping $notIncludedAdNetworks to not-Included-AdNetwork-Ids
            $notIncludedAdNetworkIds = [];
            foreach ($notIncludedAdNetworks as $adNetwork) {
                if (!$adNetwork instanceof AdNetworkInterface) {
                    continue;
                }

                $notIncludedAdNetworkIds[] = $adNetwork->getId();
            }

            // sure unique to avoid duplicate increase position
            $allTagsToBeIncreasedPosition = array_unique($allTagsToBeIncreasedPosition);

            // do increasing
            array_walk(
                $allTagsToBeIncreasedPosition,
                function(PositionInterface $adTag) use ($position, &$updateCount, $notIncludedAdNetworkIds)
                {
                    /**
                     * @var AdTagInterface $adTag
                     */
                    if ($adTag instanceof AdTagInterface && !$adTag->getAdSlot() instanceof DisplayAdSlotInterface) {
                        return; // not updating position for other types of ad slot like native ad slot
                    }

                    if ($adTag instanceof LibrarySlotTagInterface && !$adTag->getContainer() instanceof LibraryDisplayAdSlotInterface) {
                        return; // not updating position for other types of ad slot like native ad slot
                    }

                    // increase Position, notice, if and ONLY IF ad network is not in $notIncludedAdNetworks constrain!!!
                    if ($adTag->getPosition() >= $position && !in_array($adTag->getAdNetwork()->getId(), $notIncludedAdNetworkIds)) {
                        $adTag->setPosition($adTag->getPosition() + 1);
                        $updateCount ++;
                    }
                }
            );
        }

        $this->em->flush(); //!important this will help to trigger update cache listener to refresh cache

        return $updateCount;
    }
}