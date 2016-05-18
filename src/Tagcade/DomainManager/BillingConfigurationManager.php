<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use ReflectionClass;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\BillingConfigurationInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\BillingConfigurationRepositoryInterface;

class BillingConfigurationManager implements BillingConfigurationManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, BillingConfigurationRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, BillingConfigurationInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $adNetwork)
    {
        if(!$adNetwork instanceof BillingConfigurationInterface) throw new InvalidArgumentException('expect BillingConfigurationInterface object');

        $this->om->persist($adNetwork);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $adNetwork)
    {
        if(!$adNetwork instanceof BillingConfigurationInterface) throw new InvalidArgumentException('expect BillingConfigurationInterface object');

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

    public function getAllConfigurationForPublisher(PublisherInterface $publisher)
    {
        return $this->repository->getAllConfigurationForPublisher($publisher);
    }
}