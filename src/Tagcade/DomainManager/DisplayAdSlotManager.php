<?php

namespace Tagcade\DomainManager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\DomainManager\Behaviors\ValidateAdSlotSynchronizationTrait;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Repository\Core\DisplayAdSlotRepositoryInterface;
use Tagcade\Model\Core\SiteInterface;
use ReflectionClass;

class DisplayAdSlotManager implements DisplayAdSlotManagerInterface
{
    use ValidateAdSlotSynchronizationTrait;
    protected $em;
    protected $repository;
    /**
     * @var AdSlotRepositoryInterface
     */
    private $adSlotRepository;

    public function __construct(EntityManagerInterface $em, DisplayAdSlotRepositoryInterface $repository, AdSlotRepositoryInterface $adSlotRepository)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->adSlotRepository = $adSlotRepository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, DisplayAdSlotInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(DisplayAdSlotInterface $displayAdSlot)
    {
        $this->em->getConnection()->beginTransaction();

        try {
            /** @var DisplayAdSlotInterface[] $coReferenceDisplayAdSlots */
            $coReferenceDisplayAdSlots = $displayAdSlot->getCoReferencedAdSlots();

            if($coReferenceDisplayAdSlots instanceof PersistentCollection) $coReferenceDisplayAdSlots = $coReferenceDisplayAdSlots->toArray();

            // if the DisplayAdSlotLib is in the library
            // "$adSlot->getId() == null" is to guarantee that we're creating new instance of DisplayAdSlot, not updating
            if(null !== $coReferenceDisplayAdSlots && $displayAdSlot->getLibraryDisplayAdSlot()->isVisible() && count($coReferenceDisplayAdSlots) > 0 && $displayAdSlot->getId() == null) {
                $referenceDisplayAdSlot = $coReferenceDisplayAdSlots[0];

                // we are creating new ad slot from library
                // hence we have to clone current existing AdTags base on other slot that also refers to the same library
                $adTagsToBeCloned = $referenceDisplayAdSlot->getAdTags();

                /** @var AdTagInterface $adTag */
                foreach($adTagsToBeCloned as $adTag){

                    $newAdTag = clone $adTag;
                    $newAdTag->setAdSlot($displayAdSlot);
                    $newAdTag->setRefId($adTag->getRefId());
                    $newAdTag->setId(null);

                    $displayAdSlot->getAdTags()->add($newAdTag);
                }

                // Validate synchronization
                $this->prePersistValidate($displayAdSlot, $coReferenceDisplayAdSlots);
            }

            $this->em->persist($displayAdSlot);
            $this->em->getConnection()->commit();

            $this->em->flush();

        } catch (Exception $e) {
            $this->em->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(DisplayAdSlotInterface $displayAdSlot)
    {
        $libraryDisplayAdSlot = $displayAdSlot->getLibraryDisplayAdSlot();
        //1. Remove library if visible = false and co-referenced slots less than 2
        if(!$libraryDisplayAdSlot->isVisible() && count($displayAdSlot->getCoReferencedAdSlots()) < 2 ) {
            $this->em->remove($libraryDisplayAdSlot); // resulting cascade remove this ad slot
        }
        else {
            // 2. If the tag is in library then we only remove the tag itself, not the library.
            $this->em->remove($displayAdSlot);
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
    public function getAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        return $this->adSlotRepository->getDisplayAdSlotsForSite($site, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->adSlotRepository->getDisplayAdSlotsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @param DisplayAdSlotInterface $adSlot
     */
    public function persistAndFlush(DisplayAdSlotInterface $adSlot)
    {
        $this->em->persist($adSlot);
        $this->em->flush();
    }
}