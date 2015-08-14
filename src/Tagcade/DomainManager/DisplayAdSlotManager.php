<?php

namespace Tagcade\DomainManager;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Util\Codes;
use InvalidArgumentException;
use ReflectionClass;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tagcade\Entity\Core\DynamicAdSlot;
use Tagcade\Entity\Core\Expression;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Repository\Core\DisplayAdSlotRepositoryInterface;
use Tagcade\Repository\Core\DynamicAdSlotRepositoryInterface;
use Tagcade\Repository\Core\ExpressionRepositoryInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;
use Tagcade\Service\TagLibrary\ReplicatorInterface;

class DisplayAdSlotManager implements DisplayAdSlotManagerInterface
{

    protected $em;
    protected $repository;
    /**
     * @var AdSlotRepositoryInterface
     */
    private $adSlotRepository;
    private $librarySlotTagRepository;

    /**
     * @var ReplicatorInterface
     */
    private $replicator;

    public function __construct(EntityManagerInterface $em, DisplayAdSlotRepositoryInterface $repository, AdSlotRepositoryInterface $adSlotRepository, LibrarySlotTagRepositoryInterface $librarySlotTagRepository)
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
        return is_subclass_of($entity, DisplayAdSlotInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $displayAdSlot)
    {
        if(!$displayAdSlot instanceof DisplayAdSlotInterface) throw new InvalidArgumentException('expect DisplayAdSlotInterface object');

        $libraryAdSlot = $displayAdSlot->getLibraryAdSlot();
        $referenceSlot = $this->getReferencedAdSlotsForSite($libraryAdSlot, $displayAdSlot->getSite());
        if ($referenceSlot instanceof DisplayAdSlotInterface && $referenceSlot->getId() !== $displayAdSlot->getId()) {
            throw new LogicException('Cannot create more than one ad slots in the same site referring to the same library');
        }

        if (null === $displayAdSlot->getId() && true === $displayAdSlot->getLibraryAdSlot()->isVisible()) {
            $this->replicator->replicateFromLibrarySlotToSingleAdSlot($displayAdSlot->getLibraryAdSlot(), $displayAdSlot);
        }
        else {
            $this->em->persist($displayAdSlot);
            $this->em->flush();
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $displayAdSlot)
    {
        if(!$displayAdSlot instanceof DisplayAdSlotInterface) throw new InvalidArgumentException('expect DisplayAdSlotInterface object');

        /**
         * @var ExpressionRepositoryInterface $expressionRepository
         */
        $expressionRepository = $this->em->getRepository(Expression::class);
        $expressions = $expressionRepository->findBy(array('expectAdSlot' => $displayAdSlot));

        /**
         * @var DynamicAdSlotRepositoryInterface $dynamicRepository
         */
        $dynamicRepository = $this->em->getRepository(DynamicAdSlot::class);
        $defaultSlots = $dynamicRepository->findBy(array('defaultAdSlot' => $displayAdSlot));

        if (!empty($expressions) > 0 || !empty($defaultSlots) > 0) { // this ensures that there is existing dynamic slot that one of its expressions containing this slot
            throw new LogicException('Existing dynamic ad slot that is referencing to this ad slot');
        }

        $libraryDisplayAdSlot = $displayAdSlot->getLibraryAdSlot();
        //1. Remove library if co-referenced slots less than 2
        if(count($displayAdSlot->getCoReferencedAdSlots()) < 2 ) {
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