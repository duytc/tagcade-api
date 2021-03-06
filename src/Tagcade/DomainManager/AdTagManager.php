<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Entity\Core\LibraryAdTag;
use Tagcade\Entity\Core\LibrarySlotTag;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Repository\Core\AdTagRepositoryInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;
use Tagcade\Service\TagLibrary\ReplicatorInterface;

class AdTagManager implements AdTagManagerInterface
{
    protected $batchSize;
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var AdTagRepositoryInterface
     */
    protected $repository;

    /**
     * @var LibrarySlotTagRepositoryInterface
     */
    protected $librarySlotTagRepository;

    /**
     * @var ReplicatorInterface
     */
    protected $replicator;

    /**
     * @param EntityManagerInterface $em
     * @param AdTagRepositoryInterface $repository
     * @param LibrarySlotTagRepositoryInterface $librarySlotTagRepository
     * @param $batchSize
     */
    public function __construct(EntityManagerInterface $em, AdTagRepositoryInterface $repository, LibrarySlotTagRepositoryInterface $librarySlotTagRepository, $batchSize)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->librarySlotTagRepository = $librarySlotTagRepository;
        $this->batchSize = $batchSize;
    }

    /**
     * @param ReplicatorInterface $replicator
     */
    public function setReplicator(ReplicatorInterface $replicator) {
        $this->replicator = $replicator;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, AdTagInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(AdTagInterface &$adTag)
    {
        $adSlot = $adTag->getAdSlot();
        $adSlotLib = $adSlot->getLibraryAdSlot();

        if (!$adSlotLib->isVisible()) {
            return $this->saveAdTagForNotSharedAdSlot($adTag);
        }

        // Here handles save ad tag for shared ad slot
        return $this->saveAdTagForSharedAdSlot($adTag);
    }

    protected function saveAdTagForSharedAdSlot(AdTagInterface &$adTag)
    {
        $adSlot = $adTag->getAdSlot();
        $adSlotLib = $adSlot->getLibraryAdSlot();
        if (!$adSlotLib->isVisible()) {
            throw new InvalidArgumentException('expect ad tag in shared ad slot');
        }


        if (!$adTag->getLibraryAdTag()->getVisible()) {
            $adTag->getLibraryAdTag()->setVisible(true); // when slot is shared then its tag is shared as well
        }

        if($adTag->getId() !== null) {
            $this->updateAdTagOfSharedAdSlot($adTag);
        } else {
            if ($adTag->getRefId() == null) {
                $adTag->setRefId(uniqid("", true));
            }

            $createdAdTags = $this->createNewAdTagForSharedAdSlot($adSlot, $adTag);
            foreach ($createdAdTags as $t) {
                if ($t->getAdSlot()->getId() === $adTag->getAdSlot()->getId()) {
                    $adTag = $t; // update adTag instance to the newly created tag to be used by controller.
                    break;
                }
            }
        }

        $this->em->flush();

        return $adTag;
    }

    protected function updateAdTagOfSharedAdSlot(AdTagInterface $adTag)
    {
        $adSlotLib = $adTag->getAdSlot()->getLibraryAdSlot();
        //update the library slot tag as well
        $librarySlotTag = $this->librarySlotTagRepository->getByLibraryAdSlotAndRefId($adSlotLib, $adTag->getRefId());
        if (!$librarySlotTag instanceof LibrarySlotTagInterface) {
            return; // no replication occurs
        }

        $newLibraryAdTag = $adTag->getLibraryAdTag();
        $librarySlotTag->setLibraryAdTag($newLibraryAdTag);
        $librarySlotTag->setActive($adTag->isActive());
        $librarySlotTag->setPosition($adTag->getPosition());
        $librarySlotTag->setRotation($adTag->getRotation());
        $librarySlotTag->setFrequencyCap($adTag->getFrequencyCap());
        $librarySlotTag->setImpressionCap($adTag->getImpressionCap());
        $librarySlotTag->setNetworkOpportunityCap($adTag->getNetworkOpportunityCap());

        $this->em->merge($librarySlotTag);

        // support "auto increase position" feature: update for all referenced ad tags
        if ($adTag->getAutoIncreasePosition() && $adTag->getAdSlot() instanceof DisplayAdSlotInterface) {
            // increase for library slot tag
            $this->autoIncreasePositionForRelatedLibrarySlotTags($librarySlotTag);
        }

        $this->em->flush();

        // replicate Existing LibrarySlotTag To All Referenced AdTags
        if ($adTag->getAutoIncreasePosition() && $adTag->getAdSlot() instanceof DisplayAdSlotInterface) {
            $this->replicator->replicateExistingLibrarySlotTagsToAllReferencedAdTags($adSlotLib->getLibSlotTags()->toArray());
        } else {
            $this->replicator->replicateExistingLibrarySlotTagToAllReferencedAdTags($librarySlotTag);
        }
    }

    protected function createNewAdTagForSharedAdSlot(BaseAdSlotInterface $adSlot, AdTagInterface $adTag)
    {
        // create relationship in master table
        $refId = $adTag->getRefId() == null ? uniqid("", true) : $adTag->getRefId();
        $librarySlotTag = new LibrarySlotTag();
        $librarySlotTag->setActive($adTag->isActive());
        $librarySlotTag->setRotation($adTag->getRotation());
        $librarySlotTag->setPosition($adTag->getPosition());
        $librarySlotTag->setFrequencyCap($adTag->getFrequencyCap());
        $librarySlotTag->setLibraryAdSlot($adSlot->getLibraryAdSlot());
        $librarySlotTag->setLibraryAdTag($adTag->getLibraryAdTag());
        $librarySlotTag->setRefId($refId);
        $librarySlotTag->setImpressionCap($adTag->getImpressionCap());
        $librarySlotTag->setNetworkOpportunityCap($adTag->getNetworkOpportunityCap());
        $librarySlotTag->setAutoIncreasePosition($adTag->getAutoIncreasePosition());
        $this->em->persist($librarySlotTag);

        $adSlotLib = $adSlot->getLibraryAdSlot();

        // support "auto increase position" feature: update for all referenced ad tags
//        if ($adTag->getAutoIncreasePosition() && $adSlot instanceof DisplayAdSlotInterface) {
//            // increase for library slot tag
//            $this->autoIncreasePositionForRelatedLibrarySlotTags($librarySlotTag);
//        }
        $librarySlotTag->getLibraryAdSlot();

        // make sure library slot tag is inserted before it's ad tag
        $this->em->flush();

        // support "auto increase position" feature: replicate Existing LibrarySlotTag To All Referenced AdTags
        if ($adTag->getAutoIncreasePosition() && $adTag->getAdSlot() instanceof DisplayAdSlotInterface) {
            $allCreatedAdTags = [];

            // STEP.1: replicate new only current $librarySlotTag
            $createdAdTags = $this->replicator->replicateNewLibrarySlotTagToAllReferencedAdSlots($librarySlotTag);

            if (is_array($createdAdTags)) {
                $allCreatedAdTags = array_merge($allCreatedAdTags, $createdAdTags);
            }

            // increase for library slot tag
            $this->autoIncreasePositionForRelatedLibrarySlotTags($librarySlotTag);

            // STEP.2: replicate new for other librarySlotTags
            $this->replicator->replicateExistingLibrarySlotTagsToAllReferencedAdTags($adSlotLib->getLibSlotTags()->toArray());

            return $allCreatedAdTags;
        } else {
            return $this->replicator->replicateNewLibrarySlotTagToAllReferencedAdSlots($librarySlotTag);
        }
    }

    protected function saveAdTagForNotSharedAdSlot(AdTagInterface $adTag)
    {
        $adSlot = $adTag->getAdSlot();
        $librarySlot = $adSlot->getLibraryAdSlot();

        if ($librarySlot->isVisible()) {
            throw new InvalidArgumentException('expect the tag in slot that is not in any library');
        }

        $adTag->setRefId(uniqid('', true));

        // support "auto increase position" feature: update for all referenced ad tags
        if ($adTag->getAutoIncreasePosition()) {
            $this->autoIncreasePositionForAdSlotDueToAdTag($adTag);
        }

        $this->em->persist($adTag);
        $this->em->flush();

        return $adTag;
    }

    /**
     * @inheritdoc
     */
    public function delete(AdTagInterface $adTag)
    {
        $libraryAdTag = $adTag->getLibraryAdTag();
        $adSlot = $adTag->getAdSlot();
        $adSlotLib = $adSlot->getLibraryAdSlot();

        //1. Remove library if visible = false and co-referenced tags less than 2
        if((count($adTag->getCoReferencedAdTags()) < 2)) {
            $this->em->remove($libraryAdTag); // resulting cascade remove this ad tag
        }
        else if (false === $adSlotLib->isVisible()) {
            $this->em->remove($adTag); //simple remove the ad tag if its slot is not in a library
        }
        else if (true === $adSlotLib->isVisible() && ($adSlot instanceof DisplayAdSlotInterface || $adSlot instanceof NativeAdSlotInterface)) {
            // 3. if the ad slot containing this ad tag is in library, then we have to remove all ad tags in other ad slots as well
            // these ad tags must be shared from the same tag library record with visible = false or true
            $librarySlotTag = $this->librarySlotTagRepository->getByLibraryAdSlotAndRefId($adSlotLib, $adTag->getRefId());
            if(!$librarySlotTag instanceof LibrarySlotTagInterface) {
                return;
            }

            //remove the Library Slot Tag itself
            $adSlotLib->removeLibSlotTag($librarySlotTag);
            $this->em->remove($librarySlotTag);

            $this->replicator->replicateExistingLibrarySlotTagToAllReferencedAdTags($librarySlotTag, true);
        }

        $this->em->flush();
    }

    /**
     * @inheritdoc
     */
    public function createNew()
    {
        $entity = new ReflectionClass($this->repository->getClassName());
        return $entity->newInstance();
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria = [], $orderBy = null, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getAdTagsForAdSlot(ReportableAdSlotInterface $adSlot, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdSlot($adSlot, $limit, $offset);
    }

    public function getAdTagIdsForAdSlot(ReportableAdSlotInterface $adSlot, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagIdsForAdSlot($adSlot, $limit, $offset);
    }


    public function getAdTagsForSite(SiteInterface $site, $filterActive = false, $limit = null, $offset = null)
    {
        $adTags = $this->repository->getAdTagsForSite($site, $limit, $offset);

        if (true === $filterActive) {
            $filteredAdTags = array_filter(
                $adTags,
                function(AdTagInterface $adTag)
                {
                    return $adTag->isActive();
                }
            );

            return ((is_array($filteredAdTags)) ? array_values($filteredAdTags) : $filteredAdTags);
        }

        return $adTags;
    }

    public function getAdTagIdsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagIdsForSite($site, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForPublisher($publisher, $limit, $offset);
    }

    public function getActiveAdTagsIdsForPublisher(Publisherinterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getActiveAdTagsIdsForPublisher($publisher, $limit, $offset);
    }

    public function getAllActiveAdTagIds()
    {
        return $this->repository->getAllActiveAdTagIds();
    }

    /**
     * @inheritdoc
     */
    public function getAdTagsForAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdNetwork($adNetwork, $limit, $offset);
    }

    public function getAdTagIdsForAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagIdsForAdNetwork($adNetwork, $limit, $offset);
    }


    public function getAdTagsForAdNetworkFilterPublisher(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdNetworkFilterPublisher($adNetwork, $limit, $offset);
    }


    public function getAdTagsForAdNetworkAndSite(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdNetworkAndSite($adNetwork, $site, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getAdTagsForAdNetworkAndSiteWithSubPublisher(AdNetworkInterface $adNetwork, SiteInterface $site, SubPublisherInterface $subPublisher, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdNetworkAndSiteWithSubPublisher($adNetwork, $site, $subPublisher, $limit, $offset);
    }

    public function getAdTagIdsForAdNetworkAndSite(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null)
    {
        return $this->repository->getActiveAdTagIdsForAdNetworkAndSite($adNetwork, $site, $limit, $offset);
    }

    public function getAdTagsForAdNetworkAndSites(AdNetworkInterface $adNetwork, array $sites, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdNetworkAndSites($adNetwork, $sites, $limit, $offset);
    }

    public function getAdTagsForAdNetworkAndSiteFilterPublisher(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdNetworkAndSiteFilterPublisher($adNetwork, $site, $limit, $offset);
    }

    public function updateAdTagStatusForAdNetwork(AdNetworkInterface $adNetwork, $active = true)
    {
        $it = $this->em->getRepository(AdTag::class)->createQueryBuilder('t')
            ->join('t.libraryAdTag', 'lib')
            ->where('lib.adNetwork = :adNetwork')
            ->setParameter('adNetwork', $adNetwork)
            ->getQuery()->iterate();

        $count = 0;
        /** @var AdTagInterface $adTag */
        foreach ($it as $row) {
            $adTag = $row[0];
            if ($adTag->isActive() !== $active) {
                $adTag->setActive($active);
                $this->em->persist($adTag);
                $count++;
            }

            if ($count % $this->batchSize === 0 && $count > 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }

        $this->em->flush();
        $this->em->clear();
    }

    /**
     * @param BaseAdSlotInterface $adSlot
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function getSharedAdTagsForAdSlot(BaseAdSlotInterface $adSlot, $limit = null, $offset = null)
    {
        return $this->repository->getSharedAdTagsForAdSlot($adSlot, $limit, $offset);
    }


    public function getAdTagsByAdSlotAndRefId(BaseAdSlotInterface $adSlot, $refId, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsByAdSlotAndRefId($adSlot, $refId, $limit, $offset);
    }

    public function getAdTagsByLibraryAdSlotAndRefId(BaseLibraryAdSlotInterface $libraryAdSlot, $refId, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsByLibraryAdSlotAndRefId($libraryAdSlot, $refId, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getAdTagsByLibraryAdSlotAndDifferRefId(BaseLibraryAdSlotInterface $libraryAdSlot, $refId, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsByLibraryAdSlotAndDifferRefId($libraryAdSlot, $refId, $limit, $offset);
    }

    public function updateActiveStateBySingleSiteForAdNetwork(AdNetworkInterface $adNetwork, SiteInterface $site, $active = false)
    {
        $it = $this->em->getRepository(AdTag::class)->createQueryBuilder('t')
            ->join('t.libraryAdTag', 'lib')
            ->where('lib.adNetwork = :adNetwork')
            ->setParameter('adNetwork', $adNetwork)
            ->getQuery()->iterate();

        $count = 0;
        /** @var AdTagInterface $adTag */
        foreach ($it as $row) {
            $adTag = $row[0];
            if ($adTag->isActive() !== $active && $adTag->getAdSlot()->getSite()->getId() === $site->getId()) {
                $adTag->setActive($active);
                $this->em->persist($adTag);
                $count++;
            }

            if ($count % $this->batchSize === 0 && $count > 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }

        $this->em->flush();
        $this->em->clear();
    }

    public function getAllAdTagsByStatus($status)
    {
        return $this->repository->getAllAdTagsByStatus($status);
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->em;
    }

    /**
     * auto Increase Position For Related LibrarySlotTags
     *
     * @param LibrarySlotTagInterface $librarySlotTag
     */
    protected function autoIncreasePositionForRelatedLibrarySlotTags(LibrarySlotTagInterface &$librarySlotTag)
    {
        // get libraryAdSlot
        /** @var BaseLibraryAdSlotInterface $libraryAdSlot */
        $libraryAdSlot = $librarySlotTag->getLibraryAdSlot();

        // only support LibraryDisplayAdSlot
        if (!$libraryAdSlot instanceof LibraryDisplayAdSlotInterface) {
            return;
        }

        $newLibSlotTags = [];
        $includedPersistingTag = false;

        // get all old librarySlotTags of current libraryAdSlot
        $oldLibSlotTags = $libraryAdSlot->getLibSlotTags();

        // do auto increasing position
        foreach ($oldLibSlotTags as $oldLibSlotTag) {
            // add if position small than current persisting/updating ad tag
            if ($oldLibSlotTag->getId() != null && $oldLibSlotTag->getPosition() < $librarySlotTag->getPosition()) {
                $newLibSlotTags[] = $oldLibSlotTag;
                continue;
            }

            // add current persisting/updating ad tag to new ad tags array, sure add only one time!!!
            if ($oldLibSlotTag->getId() != null && $oldLibSlotTag->getPosition() == $librarySlotTag->getPosition() && !$includedPersistingTag) {
                $newLibSlotTags[] = $librarySlotTag;
                $includedPersistingTag = true;
            }

            // increase and add if position greater than or equal position of current persisting/updating ad tag
            /** @var LibrarySlotTagInterface $oldLibSlotTag */
            if ($oldLibSlotTag->getId() != null
                && $oldLibSlotTag->getPosition() >= $librarySlotTag->getPosition()
                && $oldLibSlotTag->getId() != $librarySlotTag->getId() // IMPORTANT: sure not update position of current persisting/updating library slot tag!!!
            ) {
                $oldLibSlotTag->setPosition($oldLibSlotTag->getPosition() + 1);

                $newLibSlotTags[] = $oldLibSlotTag;
            }
        }

        // update back to input $librarySlotTag
        $libraryAdSlot->setLibSlotTags(new ArrayCollection($newLibSlotTags));
        $librarySlotTag->setLibraryAdSlot($libraryAdSlot);
    }

    /**
     * auto Increase Position For AdSlot Due To WaterfallTag
     *
     * @param AdTagInterface $newAdTag
     */
    protected function autoIncreasePositionForAdSlotDueToAdTag(AdTagInterface &$newAdTag)
    {
        $adSlot = $newAdTag->getAdSlot();
        $newAdTags = [];
        $adTags = $adSlot->getAdTags();
        $includedPersistingTag = false;

        foreach ($adTags as $oldAdTag) {
            // add if position small than current persisting/updating ad tag
            if ($oldAdTag->getId() != null && $oldAdTag->getPosition() < $newAdTag->getPosition()) {
                $newAdTags[] = $oldAdTag;
                continue;
            }

            // add current persisting/updating ad tag to new ad tags array, sure add only one time!!!
            if ($oldAdTag->getId() != null && $oldAdTag->getPosition() == $newAdTag->getPosition() && !$includedPersistingTag) {
                $newAdTags[] = $newAdTag;
                $includedPersistingTag = true;
            }

            // increase and add if position greater than or equal position of current persisting/updating ad tag
            /** @var AdTagInterface $oldAdTag */
            if ($oldAdTag->getId() != null
                && $oldAdTag->getPosition() >= $newAdTag->getPosition()
                && $oldAdTag->getId() != $newAdTag->getId() // IMPORTANT: sure not update position of current persisting/updating ad tag!!!
            ) {
                $oldAdTag->setPosition($oldAdTag->getPosition() + 1);
                $newAdTags[] = $oldAdTag;
            }
        }

        $adSlot->setAdTags($newAdTags);
    }

    /**
     * @param $status
     * @return mixed
     */
    public function getAdTagsThatSetImpressionAndOpportunityCapByStatus ($status)
    {
      return  $this->repository->getAdTagsThatSetImpressionAndOpportunityCapByStatus($status);
    }

    /**
     * @param $adTagId
     * @return mixed
     */
    public function makeStandAlone ($adTagId)
    {
        $adTag = $this->find($adTagId);
        $adTagLibrary = $adTag->getLibraryAdTag();
        $adTagLibrary->setVisible(true);

        return $adTagLibrary;
    }

    /**
     * @param LibraryAdTag $libraryAdTag
     * @return mixed
     */
    public function getAdTagsHaveTheSameAdTabLib(LibraryAdTag $libraryAdTag)
    {
        return $this->repository->getAdTagsHaveTheSameAdTabLib($libraryAdTag);
    }
}