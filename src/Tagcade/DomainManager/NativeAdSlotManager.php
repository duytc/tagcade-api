<?php

namespace Tagcade\DomainManager;

use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Rhumsaa\Uuid\Console\Exception;
use Tagcade\DomainManager\Behaviors\ReplicateLibraryAdSlotDataTrait;
use Tagcade\DomainManager\Behaviors\ValidateAdSlotSynchronizationTrait;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;
use Tagcade\Repository\Core\NativeAdSlotRepositoryInterface;

class NativeAdSlotManager implements NativeAdSlotManagerInterface
{
    use ValidateAdSlotSynchronizationTrait;
    use ReplicateLibraryAdSlotDataTrait;
    protected $em;
    protected $repository;
    /**
     * @var AdSlotRepositoryInterface
     */
    private $adSlotRepository;
    protected $librarySlotTagRepository;

    public function __construct(EntityManagerInterface $em, NativeAdSlotRepositoryInterface $repository, AdSlotRepositoryInterface $adSlotRepository, LibrarySlotTagRepositoryInterface $librarySlotTagRepository)
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
        return is_subclass_of($entity, NativeAdSlotInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(NativeAdSlotInterface $nativeAdSlot)
    {
        $this->em->getConnection()->beginTransaction();

        try {

            if(null === $nativeAdSlot->getId()) {
                $nativeAdSlot = $this->replicateFromLibrarySlotToSingleAdSlot($nativeAdSlot->getLibraryAdSlot(), $nativeAdSlot);
            }
            // Validate synchronization
            $this->validateAdSlotSynchronization($nativeAdSlot);

            $this->em->persist($nativeAdSlot);
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
    public function delete(NativeAdSlotInterface $nativeAdSlot)
    {
        $libraryNativeAdSlot = $nativeAdSlot->getLibraryAdSlot();
        //1. Remove library if visible = false and co-referenced slots less than 2
        if(!$libraryNativeAdSlot->isVisible() && count($nativeAdSlot->getCoReferencedAdSlots()) < 2 ) {
            $this->em->remove($libraryNativeAdSlot); // resulting cascade remove this ad slot
        }
        else {
            // 2. If the tag is in library then we only remove the tag itself, not the library.
            $this->em->remove($nativeAdSlot);
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
    public function getNativeAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        return $this->adSlotRepository->getNativeAdSlotsForSite($site, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getNativeAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->adSlotRepository->getNativeAdSlotsForPublisher($publisher, $limit, $offset);
    }

    public function persistAndFlush(NativeAdSlotInterface $adSlot)
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