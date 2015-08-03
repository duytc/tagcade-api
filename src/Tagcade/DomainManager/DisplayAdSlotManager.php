<?php

namespace Tagcade\DomainManager;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use ReflectionClass;
use Tagcade\DomainManager\Behaviors\ReplicateLibraryAdSlotDataTrait;
use Tagcade\DomainManager\Behaviors\ValidateAdSlotSynchronizationTrait;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Repository\Core\DisplayAdSlotRepositoryInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;

class DisplayAdSlotManager implements DisplayAdSlotManagerInterface
{
    use ValidateAdSlotSynchronizationTrait;
    use ReplicateLibraryAdSlotDataTrait;

    protected $em;
    protected $repository;
    /**
     * @var AdSlotRepositoryInterface
     */
    private $adSlotRepository;
    private $librarySlotTagRepository;

    public function __construct(EntityManagerInterface $em, DisplayAdSlotRepositoryInterface $repository, AdSlotRepositoryInterface $adSlotRepository, LibrarySlotTagRepositoryInterface $librarySlotTagRepository)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->adSlotRepository = $adSlotRepository;
        $this->librarySlotTagRepository = $librarySlotTagRepository;
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

        try {
            $this->em->getConnection()->beginTransaction();
            if (null === $displayAdSlot->getId()) {
                $displayAdSlot = $this->replicateFromLibrarySlotToSingleAdSlot($displayAdSlot->getLibraryAdSlot(), $displayAdSlot);
            }
            $this->validateAdSlotSynchronization($displayAdSlot);
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
        $libraryDisplayAdSlot = $displayAdSlot->getLibraryAdSlot();
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

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->em;
    }


}