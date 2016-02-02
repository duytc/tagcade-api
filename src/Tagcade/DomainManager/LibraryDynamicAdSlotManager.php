<?php

namespace Tagcade\DomainManager;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\DomainManager\Behaviors\RemoveLibraryAdSlotTrait;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\Core\ReportableLibraryAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\LibraryDynamicAdSlotRepositoryInterface;
use Tagcade\Service\TagLibrary\AdSlotGeneratorInterface;
use Tagcade\Service\TagLibrary\ReplicatorInterface;

class LibraryDynamicAdSlotManager implements LibraryDynamicAdSlotManagerInterface
{
    use RemoveLibraryAdSlotTrait;

    protected $em;
    protected $repository;
    /**
     * @var ReplicatorInterface
     */
    private $replicator;
    /**
     * @var RonAdSlotManagerInterface
     */
    private $ronAdSlotManager;

    /** @var AdSlotGeneratorInterface */
    protected $adSlotGenerator;

    public function __construct(EntityManagerInterface $em, LibraryDynamicAdSlotRepositoryInterface $repository, ReplicatorInterface $replicator, RonAdSlotManagerInterface $ronAdSlotManager, AdSlotGeneratorInterface $adSlotGenerator)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->replicator = $replicator;
        $this->ronAdSlotManager = $ronAdSlotManager;
        $this->adSlotGenerator = $adSlotGenerator;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, LibraryDynamicAdSlotInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $entity)
    {
        if(!$entity instanceof LibraryDynamicAdSlotInterface) throw new InvalidArgumentException('expect LibraryDynamicAdSlotInterface object');

        $this->em->persist($entity);

        // create ron ad slot for new created library ad slot in expression
        $ronAdSlot = $entity->getRonAdSlot();
        if ($ronAdSlot instanceof RonAdSlotInterface) {
            //check default library ad slot
            $defaultLibraryAdSlot = $entity->getDefaultLibraryAdSlot();
            if ($defaultLibraryAdSlot instanceof ReportableLibraryAdSlotInterface) {
                $this->ronAdSlotManager->checkLibraryAdSlotReferredByRonAdSlotExistedAndCreate($defaultLibraryAdSlot, $ronAdSlot->getRonAdSlotSegments()->toArray());
            }

            //check expect library ad slots
            $libraryExpressions = $entity->getLibraryExpressions();
            /** @var LibraryExpressionInterface $libraryExpression */
            foreach($libraryExpressions as $libraryExpression) {
                $expectLibrary = $libraryExpression->getExpectLibraryAdSlot();
                if ($expectLibrary instanceof ReportableLibraryAdSlotInterface) {
                    $this->ronAdSlotManager->checkLibraryAdSlotReferredByRonAdSlotExistedAndCreate($expectLibrary, $ronAdSlot->getRonAdSlotSegments()->toArray());
                }
            }
        }

        // creating default ad slot and expression for all referencing slots if the LibraryDynamicAdSlot introduces libraryExpression
        $this->replicator->replicateLibraryDynamicAdSlotForAllReferencedDynamicAdSlots($entity);

        $this->em->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $adSlot)
    {
        if(!$adSlot instanceof LibraryDynamicAdSlotInterface) throw new InvalidArgumentException('expect LibraryDynamicAdSlotInterface object');

        $this->removeLibraryAdSlot($adSlot);
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
        $criteria = array(
            'visible' => true
        );

        return $this->repository->findBy($criteria, $orderBy = null, $limit, $offset);
    }


    /**
     * @inheritdoc
     */
    public function getLibraryDynamicAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getLibraryDynamicAdSlotsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function generateAdSlotFromLibraryForChannelsAndSites(LibraryDynamicAdSlotInterface $slotLibrary, $channels, $sites)
    {
        $this->adSlotGenerator->generateAdSlotFromLibraryForChannelsAndSites($slotLibrary, $channels, $sites);
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->em;
    }


}