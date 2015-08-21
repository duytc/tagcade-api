<?php

namespace Tagcade\Service\Core\AdTag;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\DomainManager\LibrarySlotTagManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\Core\PositionInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Service\TagLibrary\ChecksumValidatorInterface;

class AdTagPositionEditor implements AdTagPositionEditorInterface
{
    /**
     * @var AdTagManagerInterface
     */
    private $adTagManager;

    /**
     * @var LibrarySlotTagManagerInterface
     */
    private $librarySlotTagManager;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ChecksumValidatorInterface
     */
    private $validator;

    function __construct(AdTagManagerInterface $adTagManager, LibrarySlotTagManagerInterface $librarySlotTagManager,  EntityManagerInterface $em)
    {
        $this->adTagManager = $adTagManager;
        $this->em = $em;
        $this->librarySlotTagManager = $librarySlotTagManager;
    }

    public function setValidator(ChecksumValidatorInterface $validator) {
        $this->validator = $validator;
    }

    public function setAdTagPositionForAdNetworkAndSites(AdNetworkInterface $adNetwork, $position, $sites = null)
    {
        if (!is_int($position) || $position < 1) {
            throw new InvalidArgumentException('expect positive integer for ad tag position');
        }

        if (null === $sites || (is_array($sites) && count($sites) < 1)) {
            return $this->setAdTagPositionForAdNetwork($adNetwork, $position);
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

        $adTags = $this->adTagManager->getAdTagsForAdNetworkAndSites($adNetwork, $filterSites);

        return $this->updatePosition($adTags, $position);

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
                            $librarySlotTag = $this->librarySlotTagManager->getByLibraryAdSlotAndRefId($libAdSlot, $adTag->getRefId());
                            if ($librarySlotTag instanceof LibrarySlotTagInterface) {
                                $librarySlotTag->setPosition($pos);
                            }
                        }
                        //update all referenced AdTags if they are shared ad slot library
                        $referencedTags = $this->adTagManager->getAdTagsByLibraryAdSlotAndRefId($libAdSlot, $adTag->getRefId());
                        if(!empty($referencedTags)) {
                            array_walk($referencedTags, function(AdTagInterface $t) use($pos) { $t->setPosition($pos); });
                        }
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
     * @param AdNetworkInterface $adNetwork
     * @param int $position
     * @return int number of ad tags get position updated
     */
    protected function setAdTagPositionForAdNetwork(AdNetworkInterface $adNetwork, $position)
    {
        $adTags = $this->adTagManager->getAdTagsForAdNetwork($adNetwork);

        return $this->updatePosition($adTags, $position);
    }

    protected function updatePosition(array $adTags, $position)
    {
        $allTagsToBeUpdated = $adTags;
        foreach($adTags as $adTag) {
            /**
             * @var AdTagInterface $adTag
             */
            //update to slot tag if this is a shared ad slot
            $libAdSlot = $adTag->getAdSlot()->getLibraryAdSlot();
            // update position for library slot tag
            $librarySlotTag = $this->librarySlotTagManager->getByLibraryAdSlotAndRefId($libAdSlot, $adTag->getRefId());
            if ($librarySlotTag instanceof LibrarySlotTagInterface) {
                if (!in_array($librarySlotTag, $allTagsToBeUpdated)) {
                    $allTagsToBeUpdated[] = $librarySlotTag;
                }
            }

            //update all referenced AdTags if they are shared ad slot library
            $referencedTags = $this->adTagManager->getAdTagsByLibraryAdSlotAndRefId($libAdSlot, $adTag->getRefId());
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
        }

        $updateCount = 0;
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

        $this->em->flush(); //!important this will help to trigger update cache listener to refresh cache

        return $updateCount;
    }
}