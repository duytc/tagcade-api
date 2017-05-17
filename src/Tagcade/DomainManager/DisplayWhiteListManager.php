<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\DisplayWhiteListInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\DisplayWhiteListRepositoryInterface;

class DisplayWhiteListManager implements DisplayWhiteListManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, DisplayWhiteListRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, DisplayWhiteListInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $channel)
    {
        if (!$channel instanceof DisplayWhiteListInterface) throw new InvalidArgumentException('expect DisplayWhiteListInterface object');
        $this->om->persist($channel);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $channel)
    {
        if (!$channel instanceof DisplayWhiteListInterface) throw new InvalidArgumentException('expect DisplayWhiteListInterface object');
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

    /**
     * @inheritdoc
     */
    public function getDisplayWhiteListsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getDisplayWhiteListsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @param AdNetworkInterface $adNetwork
     * @return mixed
     */
    public function getByAdNetwork(AdNetworkInterface $adNetwork)
    {
        return $this->repository->getWhiteListsForAdNetwork($adNetwork);
    }
}