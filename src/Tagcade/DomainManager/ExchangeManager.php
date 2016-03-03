<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use ReflectionClass;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\ExchangeInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Repository\Core\ExchangeRepositoryInterface;

class ExchangeManager implements ExchangeManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, ExchangeRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, ExchangeInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $adNetwork)
    {
        if(!$adNetwork instanceof ExchangeInterface) throw new InvalidArgumentException('expect ExchangeInterface object');

        $this->om->persist($adNetwork);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $adNetwork)
    {
        if(!$adNetwork instanceof ExchangeInterface) throw new InvalidArgumentException('expect ExchangeInterface object');

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

    public function getExchangeByName($name)
    {
        return $this->repository->getExchangeByName($name);
    }

    public function getExchangeByCanonicalName($name)
    {
        return $this->repository->getExchangeByCanonicalName($name);
    }
}