<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AdNetworkManagerInterface
{
    /**
     * @see \Tagcade\DomainManager\ManagerInterface
     *
     * @param AdNetworkInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * @param AdNetworkInterface $adNetwork
     * @return void
     */
    public function save(AdNetworkInterface $adNetwork);

    /**
     * @param AdNetworkInterface $adNetwork
     * @return void
     */
    public function delete(AdNetworkInterface $adNetwork);

    /**
     * @return AdNetworkInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return AdNetworkInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return AdNetworkInterface[]
     */
    public function all($limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return AdNetworkInterface[]
     */
    public function getAdNetworksForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);
}