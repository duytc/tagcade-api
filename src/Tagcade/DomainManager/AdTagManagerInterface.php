<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Core\SiteInterface;

interface AdTagManagerInterface
{
    /**
     * @see \Tagcade\DomainManager\ManagerInterface
     *
     * @param AdTagInterface|string $entity
     * @return bool
     */
    public function supportsEntity($entity);

    /**
     * @param AdTagInterface $adTag
     * @return void
     */
    public function save(AdTagInterface $adTag);

    /**
     * @param AdTagInterface $adTag
     * @return void
     */
    public function delete(AdTagInterface $adTag);

    /**
     * @return AdTagInterface
     */
    public function createNew();

    /**
     * @param int $id
     * @return AdTagInterface|null
     */
    public function find($id);

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function all($limit = null, $offset = null);

    /**
     * @param AdSlotInterface $adSlot
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function getAdTagsForAdSlot(AdSlotInterface $adSlot, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function getAdTagsForPublisher(Publisherinterface $publisher, $limit = null, $offset = null);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function getAdTagsForAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

    public function getAdTagsForAdNetworkAndSite(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null);

    /**
     * @param adTagInterface[] $adTags
     * @param array $newAdTagOrderIds
     * @return adTagInterface[]
     */
    public function reorderAdTags(array $adTags, array $newAdTagOrderIds);
}