<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use ReflectionClass;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\IvtPixelInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\IvtPixelRepositoryInterface;

class IvtPixelManager implements IvtPixelManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, IvtPixelRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, IvtPixelInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $ivtPixel)
    {
        if(!$ivtPixel instanceof IvtPixelInterface) throw new InvalidArgumentException('expect IvtPixelInterface object');

        $this->om->persist($ivtPixel);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $ivtPixel)
    {
        if(!$ivtPixel instanceof IvtPixelInterface) throw new InvalidArgumentException('expect IvtPixelInterface object');

        $this->om->remove($ivtPixel);
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
    public function getIvtPixelsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getIvtPixelsForPublisher($publisher, $limit, $offset);
    }
}