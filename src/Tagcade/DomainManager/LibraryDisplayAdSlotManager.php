<?php

namespace Tagcade\DomainManager;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\DomainManager\Behaviors\RemoveLibraryAdSlotTrait;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\LibraryDisplayAdSlotRepositoryInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;
use Tagcade\Service\TagLibrary\AdSlotGeneratorInterface;

class LibraryDisplayAdSlotManager implements LibraryDisplayAdSlotManagerInterface
{
    use RemoveLibraryAdSlotTrait;

    protected $em;
    protected $repository;
    protected $librarySlotTagRepository;

    /** @var AdSlotGeneratorInterface */
    protected $adSlotGenerator;

    public function __construct(EntityManagerInterface $em, LibraryDisplayAdSlotRepositoryInterface $repository, LibrarySlotTagRepositoryInterface $librarySlotTagRepository, AdSlotGeneratorInterface $adSlotGenerator)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->librarySlotTagRepository = $librarySlotTagRepository;
        $this->adSlotGenerator = $adSlotGenerator;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, LibraryDisplayAdSlotInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $adSlot)
    {
        if(!$adSlot instanceof LibraryDisplayAdSlotInterface) throw new InvalidArgumentException('expect LibraryDisplayAdSlotInterface object');

        $this->em->persist($adSlot);
        $this->em->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $adSlot)
    {
        if(!$adSlot instanceof LibraryDisplayAdSlotInterface) throw new InvalidArgumentException('expect LibraryDisplayAdSlotInterface object');

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
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return LibraryDisplayAdSlotInterface[]
     */
    public function getLibraryDisplayAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getLibraryDisplayAdSlotsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function generateAdSlotFromLibraryForChannelsAndSites(LibraryDisplayAdSlotInterface $slotLibrary, $channels, $sites)
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