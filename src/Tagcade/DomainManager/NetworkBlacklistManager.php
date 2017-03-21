<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\DisplayBlacklistInterface;
use Tagcade\Model\Core\NetworkBlacklistInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\NetworkBlacklistRepositoryInterface;

class NetworkBlacklistManager implements NetworkBlacklistManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, NetworkBlacklistRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, NetworkBlacklistInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $channel)
    {
        if (!$channel instanceof NetworkBlacklistInterface) throw new InvalidArgumentException('expect NetworkBlacklistInterface object');
        $this->om->persist($channel);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $channel)
    {
        if (!$channel instanceof NetworkBlacklistInterface) throw new InvalidArgumentException('expect NetworkBlacklistInterface object');
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
    public function getDisplayBlacklistsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getDisplayBlacklistsForPublisher($publisher);
    }

    /**
     * @param AdNetworkInterface $adNetwork
     * @return mixed
     */
    public function getByAdNetwork(AdNetworkInterface $adNetwork)
    {
        return $this->repository->getBlacklistsForAdNetwork($adNetwork);
    }

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getDefaultBlacklists(PublisherInterface $publisher, $limit = null, $offset = null){
        return $this->repository->getDefaultBlacklists($publisher);
    }

    /**
     * @param PublisherInterface $publisher
     * @param DisplayBlacklistInterface $displayBlacklist
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function setDefaultBlacklists(PublisherInterface $publisher, DisplayBlacklistInterface $displayBlacklist, $limit = null, $offset = null)
    {
        return $this->repository->setDefaultBlacklists($publisher, $displayBlacklist);
    }
}