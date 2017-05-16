<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Model\Core\NetworkWhiteListInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\NetworkWhiteListRepositoryInterface;

class NetworkWhiteListManager implements NetworkWhiteListManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, NetworkWhiteListRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, NetworkWhiteListInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $channel)
    {
        if (!$channel instanceof NetworkWhiteListInterface) throw new InvalidArgumentException('expect NetworkWhiteListInterface object');
        $this->om->persist($channel);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $channel)
    {
        if (!$channel instanceof NetworkWhiteListInterface) throw new InvalidArgumentException('expect NetworkWhiteListInterface object');
        $this->om->remove($channel);
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
        return $this->repository->all($limit, $offset);
    }

    public function getByPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getNetworkWhiteListForPublisher($publisher, $limit, $offset);
    }
}