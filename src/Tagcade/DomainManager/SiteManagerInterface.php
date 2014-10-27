<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Core\AdNetworkInterface;

interface SiteManagerInterface
{
    /**
     * @see \Tagcade\DomainManager\ManagerInterface
     *
     * @param SiteInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * @param SiteInterface $site
     * @return void
     */
    public function save(SiteInterface $site);

    /**
     * @param SiteInterface $site
     * @return void
     */
    public function delete(SiteInterface $site);

    /**
     * @return SiteInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return SiteInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return SiteInterface[]
     */
    public function all($limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return SiteInterface[]
     */
    public function getSitesForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getSitesThatHaveAdTagsBelongingToAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null);
}