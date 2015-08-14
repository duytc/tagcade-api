<?php

namespace Tagcade\DomainManager;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Entity\Core\DynamicAdSlot;
use Tagcade\Entity\Core\Expression;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Repository\Core\DynamicAdSlotRepositoryInterface;
use Tagcade\Repository\Core\ExpressionRepositoryInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;
use Tagcade\Repository\Core\NativeAdSlotRepositoryInterface;
use Tagcade\Service\TagLibrary\ReplicatorInterface;

class NativeAdSlotManager implements NativeAdSlotManagerInterface
{
    protected $em;
    protected $repository;
    /**
     * @var AdSlotRepositoryInterface
     */
    private $adSlotRepository;
    protected $librarySlotTagRepository;
    /**
     * @var ReplicatorInterface
     */
    protected $replicator;

    public function __construct(EntityManagerInterface $em, NativeAdSlotRepositoryInterface $repository, AdSlotRepositoryInterface $adSlotRepository, LibrarySlotTagRepositoryInterface $librarySlotTagRepository)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->adSlotRepository = $adSlotRepository;
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
        return is_subclass_of($entity, NativeAdSlotInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $nativeAdSlot)
    {
        if(!$nativeAdSlot instanceof NativeAdSlotInterface) throw new InvalidArgumentException('expect NativeAdSlotInterface object');

        $libraryAdSlot = $nativeAdSlot->getLibraryAdSlot();
        $referenceSlot = $this->getReferencedAdSlotsForSite($libraryAdSlot, $nativeAdSlot->getSite());
        if ($referenceSlot instanceof NativeAdSlotInterface && $referenceSlot->getId() !== $nativeAdSlot->getId()) {
            throw new LogicException('Cannot create more than one ad slots in the same site referring to the same library');
        }


        if(null === $nativeAdSlot->getId() && $nativeAdSlot->getLibraryAdSlot()->isVisible()) {
            $this->replicator->replicateFromLibrarySlotToSingleAdSlot($nativeAdSlot->getLibraryAdSlot(), $nativeAdSlot);
        }
        else {
            $this->em->persist($nativeAdSlot);
            $this->em->flush();
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $nativeAdSlot)
    {
        if(!$nativeAdSlot instanceof NativeAdSlotInterface) throw new InvalidArgumentException('expect NativeAdSlotInterface object');

        /**
         * @var ExpressionRepositoryInterface $expressionRepository
         */
        $expressionRepository = $this->em->getRepository(Expression::class);
        $expressions = $expressionRepository->findBy(array('expectAdSlot' => $nativeAdSlot));

        /**
         * @var DynamicAdSlotRepositoryInterface $dynamicRepository
         */
        $dynamicRepository = $this->em->getRepository(DynamicAdSlot::class);
        $defaultSlots = $dynamicRepository->findBy(array('defaultAdSlot' => $nativeAdSlot));

        if (!empty($expressions) > 0 || !empty($defaultSlots) > 0) { // this ensures that there is existing dynamic slot that one of its expressions containing this slot
            throw new LogicException('Existing dynamic ad slot that is referencing to this ad slot');
        }

        $libraryNativeAdSlot = $nativeAdSlot->getLibraryAdSlot();
        //1. Remove library this ad slot is the only one that refer to the library
        if(count($nativeAdSlot->getCoReferencedAdSlots()) < 2 ) {
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

    /**
     * Get all referenced ad slots that refer to the same library and on the same site to current slot
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param SiteInterface $site
     * @return mixed
     */
    public function getReferencedAdSlotsForSite(BaseLibraryAdSlotInterface $libraryAdSlot, SiteInterface $site)
    {
        return $this->adSlotRepository->getReferencedAdSlotsForSite($libraryAdSlot, $site);
    }
}