<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use ReflectionClass;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdNetworkPartnerRepositoryInterface;

class AdNetworkPartnerManager implements AdNetworkPartnerManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, AdNetworkPartnerRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, AdNetworkPartnerInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $adNetwork)
    {
        if (!$adNetwork instanceof AdNetworkPartnerInterface) throw new InvalidArgumentException('expect AdNetworkPartnerInterface object');

        $this->om->persist($adNetwork);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $adNetwork)
    {
        if (!$adNetwork instanceof AdNetworkPartnerInterface) throw new InvalidArgumentException('expect AdNetworkInterface object');

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

    public function getAdNetworkPartnersForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->findByPublisher($publisher->getId());
    }

    public function getByCanonicalName($name)
    {
        return $this->repository->findByCanonicalName($name);
    }
}