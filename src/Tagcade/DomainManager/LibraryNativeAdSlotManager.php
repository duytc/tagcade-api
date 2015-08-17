<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\DomainManager\Behaviors\RemoveLibraryAdSlotTrait;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\LibraryNativeAdSlotRepositoryInterface;

class LibraryNativeAdSlotManager implements LibraryNativeAdSlotManagerInterface
{
    use RemoveLibraryAdSlotTrait;

    protected $em;
    protected $repository;

    public function __construct(EntityManagerInterface $em, LibraryNativeAdSlotRepositoryInterface $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, LibraryNativeAdSlotInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $adSlot)
    {
        if(!$adSlot instanceof LibraryNativeAdSlotInterface) throw new InvalidArgumentException('expect LibraryNativeAdSlotInterface object');

        $this->em->persist($adSlot);
        $this->em->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $adSlot)
    {
        if(!$adSlot instanceof LibraryNativeAdSlotInterface) throw new InvalidArgumentException('expect LibraryNativeAdSlotInterface object');

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
    public function getLibraryNativeAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getLibraryNativeAdSlotsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->em;
    }


}