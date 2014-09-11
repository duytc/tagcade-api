<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Repository\Core\AdNetworkRepositoryInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use ReflectionClass;

class AdNetworkManager implements AdNetworkManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, AdNetworkRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, AdNetworkInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(AdNetworkInterface $adNetwork)
    {
        $this->om->persist($adNetwork);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(AdNetworkInterface $adNetwork)
    {
        $this->om->remove($adNetwork);
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
    public function getAdNetworksForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getAdNetworksForPublisher($publisher, $limit, $offset);
    }
}