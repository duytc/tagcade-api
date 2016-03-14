<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\SegmentRepositoryInterface;

class SegmentManager implements SegmentManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, SegmentRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, SegmentInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $site)
    {
        if (!$site instanceof SegmentInterface) throw new InvalidArgumentException('expect SegmentInterface object');

        $this->om->persist($site);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $site)
    {
        if (!$site instanceof SegmentInterface) throw new InvalidArgumentException('expect SegmentInterface object');

        $this->om->remove($site);
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
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getSegmentsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getSegmentsForPublisher($publisher, $limit, $offset);
    }



    /**
     * @param PublisherInterface $publisher
     * @param null $type
     * @return array
     */
    public function getSegmentsByType(PublisherInterface $publisher, $type = null)
    {
        return $this->repository->getSegmentsByType($publisher, $type);
    }

}