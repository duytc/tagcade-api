<?php

namespace Tagcade\DomainManager;

use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Tagcade\Entity\Core\LibrarySlotTag;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdTagRepositoryInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;
use Tagcade\Service\TagLibrary\ReplicatorInterface;

class AdTagManager implements AdTagManagerInterface
{

    /**
     * @var EntityManagerInterface
     */
    protected $em;
    protected $repository;
    /**
     * @var LibrarySlotTagRepositoryInterface
     */
    protected $librarySlotTagRepository;

    /**
     * @var ReplicatorInterface
     */
    protected $replicator;

    public function __construct(EntityManagerInterface $em, AdTagRepositoryInterface $repository, LibrarySlotTagRepositoryInterface $librarySlotTagRepository)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->librarySlotTagRepository = $librarySlotTagRepository;
    }

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

        $this->em->merge($librarySlotTag);

        $this->replicator->replicateExistingLibrarySlotTagToAllReferencedAdTags($librarySlotTag);
    }

    protected function createNewAdTagForSharedAdSlot(BaseAdSlotInterface $adSlot, AdTagInterface $adTag)
    {
        // create relationship in master table
        $refId = uniqid("", true);
        $librarySlotTag = new LibrarySlotTag();
        $librarySlotTag->setActive($adTag->isActive());
        $librarySlotTag->setRotation($adTag->getRotation());
        $librarySlotTag->setPosition($adTag->getPosition());
        $librarySlotTag->setFrequencyCap($adTag->getFrequencyCap());
        $librarySlotTag->setLibraryAdSlot($adSlot->getLibraryAdSlot());
        $librarySlotTag->setLibraryAdTag($adTag->getLibraryAdTag());
        $librarySlotTag->setRefId($refId);

        $this->em->persist($librarySlotTag);

        return $this->replicator->replicateNewLibrarySlotTagToAllReferencedAdSlots($librarySlotTag);
    }

    protected function saveAdTagForNotSharedAdSlot(AdTagInterface $adTag)
    {
        $adSlot = $adTag->getAdSlot();
        $librarySlot = $adSlot->getLibraryAdSlot();

        if ($librarySlot->isVisible()) {
            throw new InvalidArgumentException('expect the tag in slot that is not in any library');
        }

        $adTag->setRefId(uniqid('', true));

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

    public function getAdTagsForSite(SiteInterface $site, $filterActive = false, $limit = null, $offset = null)
    {
        $adTags = $this->repository->getAdTagsForSite($site, $limit, $offset);

        if (true === $filterActive) {
            return array_filter(
                $adTags,
                function(AdTagInterface $adTag)
                {
                    return $adTag->isActive();
                }
            );
        }

        return $adTags;
    }

    /**
     * @inheritdoc
     */
    public function getAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getAdTagsForAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdNetwork($adNetwork, $limit, $offset);
    }

    public function getAdTagsForAdNetworkFilterPublisher(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdNetworkFilterPublisher($adNetwork, $limit, $offset);
    }


    public function getAdTagsForAdNetworkAndSite(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null)
    {
        return $this->repository->getAdTagsForAdNetworkAndSite($adNetwork, $site, $limit, $offset);
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
        $adTags = $this->getAdTagsForAdNetwork($adNetwork);

        /**
         * @var AdTagInterface $adTag
         */
        foreach($adTags as $adTag) {
            if ($adTag->isActive() !== $active) {
                $adTag->setActive($active);
                $this->save($adTag);
            }
        }
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
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->em;
    }


}