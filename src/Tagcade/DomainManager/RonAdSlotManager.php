<?php

namespace Tagcade\DomainManager;

use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Tagcade\Entity\Core\LibraryDynamicAdSlot;
use Tagcade\Entity\Core\LibraryExpression;
use Tagcade\Entity\Core\RonAdSlot;
use Tagcade\Entity\Core\RonAdSlotSegment;
use Tagcade\Entity\Core\Site;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\Core\ReportableLibraryAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotSegmentInterface;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\LibraryDynamicAdSlotRepositoryInterface;
use Tagcade\Repository\Core\LibraryExpressionRepositoryInterface;
use Tagcade\Repository\Core\RonAdSlotRepositoryInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;
use Tagcade\Service\StringUtil;
use Tagcade\Service\StringUtilTrait;
use Tagcade\Service\TagLibrary\AdSlotGeneratorInterface;

class RonAdSlotManager implements RonAdSlotManagerInterface
{
    use StringUtilTrait;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var RonAdSlotRepositoryInterface
     */
    private $repository;

    /**
     * @var SiteRepositoryInterface
     */
    private $siteRepository;

    /**
     * @var AdSlotGeneratorInterface
     */
    private $adSlotGenerator;
    /**
     * @var AdSlotManagerInterface
     */
    private $adSlotManager;

    function __construct(EntityManagerInterface $em, RonAdSlotRepositoryInterface $repository, SiteRepositoryInterface $siteRepository, AdSlotManagerInterface $adSlotManager)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->siteRepository = $siteRepository;
        $this->adSlotManager = $adSlotManager;
    }

    public function setAdSlotGenerator(AdSlotGeneratorInterface $adSlotGenerator)
    {
        $this->adSlotGenerator = $adSlotGenerator;
    }

    /**
     * Should take an object instance or string class name
     * Should return true if the supplied entity object or class is supported by this manager
     *
     * @param ModelInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, RonAdSlotInterface::class);
    }

    /**
     * @param ModelInterface $entity
     * @return void
     */
    public function save(ModelInterface $entity)
    {
        if (!$entity instanceof RonAdSlotInterface) {
            throw new InvalidArgumentException('expect RonAdSlotInterface object');
        }

        //creating a new ron ad slot
        if ($entity->getId() === null) {
            $libraryAdSlot = $entity->getLibraryAdSlot();
            if ($libraryAdSlot instanceof LibraryDynamicAdSlotInterface) {
                //check default library ad slot
                $defaultLibraryAdSlot = $libraryAdSlot->getDefaultLibraryAdSlot();
                if ($defaultLibraryAdSlot instanceof ReportableLibraryAdSlotInterface) {
                    $this->checkLibraryAdSlotReferredByRonAdSlotExistedAndCreate($defaultLibraryAdSlot, $entity->getRonAdSlotSegments()->toArray());
                }

                //check expect library ad slots
                $libraryExpressions = $libraryAdSlot->getLibraryExpressions();
                /** @var LibraryExpressionInterface $libraryExpression */
                foreach($libraryExpressions as $libraryExpression) {
                    $expectLibrary = $libraryExpression->getExpectLibraryAdSlot();
                    if ($expectLibrary instanceof ReportableLibraryAdSlotInterface) {
                        $this->checkLibraryAdSlotReferredByRonAdSlotExistedAndCreate($expectLibrary, $entity->getRonAdSlotSegments()->toArray());
                    }
                }
            }
        }

        $this->em->persist($entity);
        $this->em->flush();
    }

    public function checkLibraryAdSlotReferredByRonAdSlotExistedAndCreate(ReportableLibraryAdSlotInterface $libraryAdSlot, array $ronAdSlotSegments)
    {
        if ($libraryAdSlot->getRonAdSlot() instanceof RonAdSlotInterface) {
            return;
        }

        $ronAdSlot = new RonAdSlot();
        $ronAdSlot->setLibraryAdSlot($libraryAdSlot);

        /** @var RonAdSlotSegmentInterface $ronAdSlotSegment */
        foreach($ronAdSlotSegments as $ronAdSlotSegment) {
            $newRonSegment = clone $ronAdSlotSegment;
            $newRonSegment->setRonAdSlot($ronAdSlot);
            $ronAdSlot->addRonAdSlotSegment($newRonSegment);
        }

        $libraryAdSlot->setRonAdSlot($ronAdSlot);
        $this->em->persist($ronAdSlot);
        $this->em->flush();
    }

    /**
     * @param ModelInterface $entity
     * @return void
     */
    public function delete(ModelInterface $entity)
    {
        if (!$entity instanceof RonAdSlotInterface) {
            throw new InvalidArgumentException('expect RonAdSlotInterface object');
        }

        $adSlots = $this->adSlotManager->getAdSlotsForRonAdSlot($entity);
        if (!empty($adSlots)) {
            throw new LogicException('this ron ad slot had been referred by another ad slot');
        }

        if ($this->checkIfRonAdSlotIsReferredByDynamicRonAdSlot($entity)) {
            throw new LogicException('There are some dynamic ron ad slot still referring to this ron ad slot');
        }

        $this->em->remove($entity);
        $this->em->flush();
    }

    protected function checkIfRonAdSlotIsReferredByDynamicRonAdSlot(RonAdSlotInterface $ronAdSlot)
    {
        $libraryAdSlot = $ronAdSlot->getLibraryAdSlot();
        if (!$libraryAdSlot instanceof ReportableLibraryAdSlotInterface) {
            return false;
        }
        /**
         * @var LibraryExpressionRepositoryInterface $libraryExpressionRepository
         */
        $libraryExpressionRepository = $this->em->getRepository(LibraryExpression::class);
        /** @var LibraryExpressionInterface[] $libraryExpressions */
        $libraryExpressions = $libraryExpressionRepository->getByExpectLibraryAdSlot($libraryAdSlot);
        /**
         * @var LibraryDynamicAdSlotRepositoryInterface $libraryDynamicAdSlotRepository
         */
        $libraryDynamicAdSlotRepository = $this->em->getRepository(LibraryDynamicAdSlot::class);
        $libraryDynamicAdSlots = $libraryDynamicAdSlotRepository->getByDefaultLibraryAdSlot($libraryAdSlot);

        foreach($libraryExpressions as $libraryExpression) {
            if ($libraryExpression->getLibraryDynamicAdSlot()->getRonAdSlot() instanceof RonAdSlotInterface) {
                return true;
            }
        }

        foreach($libraryDynamicAdSlots as $libraryDynamicAdSlot) {
            if ($libraryDynamicAdSlot->getRonAdSlot() instanceof RonAdSlotInterface) {
                return true;
            }
        }

        return false;
    }
    /**
     * @return ModelInterface
     */
    public function createNew()
    {
        $entity = new ReflectionClass($this->repository->getClassName());
        return $entity->newInstance();
    }

    /**
     * @param int $id
     * @return ModelInterface|null
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return ModelInterface[]
     */
    public function all($limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria = [], $orderBy = null, $limit, $offset);
    }

    /**
     * Get all RonAdSlot that a specific publisher had created before
     *
     * @param PublisherInterface $publisher
     * @param null|int $limit
     * @param null|int $offset
     * @return array
     */
    public function getRonAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getRonAdSlotsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @param SegmentInterface $segment
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getRonAdSlotsForSegment(SegmentInterface $segment, $limit = null, $offset = null)
    {
        return $this->repository->getRonAdSlotsForSegment($segment, $limit, $offset);
    }


    /**
     *
     * @param RonAdSlotInterface $ronAdSlot
     * @param $domain
     * @return BaseAdSlotInterface
     * @throw LogicException
     */
    public function createAdSlotFromRonAdSlotAndDomain(RonAdSlotInterface $ronAdSlot, $domain)
    {
        $libraryAdSlot = $ronAdSlot->getLibraryAdSlot();
        if (!$libraryAdSlot instanceof BaseLibraryAdSlotInterface) {
            throw new LogicException('Invalid RonAdSlot');
        }
        
        $domain = $this->extractDomain($domain);
        $site = $this->siteRepository->getSiteByDomainAndPublisher($libraryAdSlot->getPublisher(), $domain);
        // if site with $domain not yet created
        if (!$site instanceof SiteInterface) {
            // create new Site
            $site = new Site();
            $site->setDomain($domain)
                ->setName($domain)
                ->setEnableSourceReport(false)
                ->setPublisher($libraryAdSlot->getPublisher())
                ->setAutoCreate(true);
            $this->em->persist($site);
            $this->em->flush();
        }

        /**@var BaseAdSlotInterface $adSlot */
        $adSlot = $this->adSlotGenerator->generateAdSlotFromLibraryForSites($libraryAdSlot, [$site], $returnAdSlot = 1);

        $adSlot->setAutoCreate(true);
        $this->em->merge($adSlot);
        $this->em->flush();

        return $adSlot;
    }

    /**
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getRonAdSlotsWithoutSegment($limit = null, $offset = null)
    {
        $result = [];
        $ronAdSlots = $this->repository->findAll();
        /** @var RonAdSlotInterface $ronAdSlot */
        foreach($ronAdSlots as $ronAdSlot) {
            $segments = $ronAdSlot->getSegments();
            if (count($segments) < 1) {
                $result[] = $ronAdSlot;
            }
        }
        return $result;
    }
}