<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Repository\Core\DynamicAdSlotRepositoryInterface;

class DynamicAdSlotManager implements DynamicAdSlotManagerInterface
{
    protected $om;
    protected $repository;
    /**
     * @var AdSlotRepositoryInterface
     */
    private $adSlotRepository;

    public function __construct(ObjectManager $om, DynamicAdSlotRepositoryInterface $repository, AdSlotRepositoryInterface $adSlotRepository)
    {
        $this->om = $om;
        $this->repository = $repository;
        $this->adSlotRepository = $adSlotRepository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, DynamicAdSlotInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $dynamicAdSlot)
    {
        if(!$dynamicAdSlot instanceof DynamicAdSlotInterface) throw new InvalidArgumentException('expect DynamicAdSlotInterface object');

        $libraryAdSlot = $dynamicAdSlot->getLibraryAdSlot();
        $referenceSlot = $this->getReferencedAdSlotsForSite($libraryAdSlot, $dynamicAdSlot->getSite());
        if ($referenceSlot instanceof DynamicAdSlotInterface && $referenceSlot->getId() !== $dynamicAdSlot->getId()) {
            throw new LogicException('Cannot create more than one ad slots in the same site referring to the same library');
        }

        $this->om->persist($dynamicAdSlot);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $dynamicAdSlot)
    {
        if(!$dynamicAdSlot instanceof DynamicAdSlotInterface) throw new InvalidArgumentException('expect DynamicAdSlotInterface object');

        $libraryDynamicAdSlot = $dynamicAdSlot->getLibraryAdSlot();
        //1. Remove library this ad slot is the only one that refer to the library
        if(count($dynamicAdSlot->getCoReferencedAdSlots()) < 2 ) {
            $this->om->remove($libraryDynamicAdSlot); // resulting cascade remove this ad slot
        }
        else {
            // 2. If the tag is in library then we only remove the tag itself, not the library.
            $this->om->remove($dynamicAdSlot);
        }

        $this->om->flush();
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
    public function getDynamicAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        return $this->adSlotRepository->getDynamicAdSlotsForSite($site, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getDynamicAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->adSlotRepository->getDynamicAdSlotsForPublisher($publisher, $limit, $offset);
    }

    public function persistAndFlush(DynamicAdSlotInterface $adSlot)
    {
        $this->om->persist($adSlot);
        $this->om->flush();
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

    /**
     * Get all dynamic ad slots that have default ad slot $adSlot
     * @param ReportableAdSlotInterface $adSlot
     * @return array
     */
    public function getDynamicAdSlotsThatHaveDefaultAdSlot(ReportableAdSlotInterface $adSlot)
    {
        return $this->repository->getDynamicAdSlotsThatHaveDefaultAdSlot($adSlot);
    }


}