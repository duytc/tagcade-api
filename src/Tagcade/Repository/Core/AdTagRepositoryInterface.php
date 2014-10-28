<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Core\SiteInterface;

interface AdTagRepositoryInterface extends ObjectRepository
{
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
    public function getAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getAdTagsForAdNetworkQuery(AdNetworkInterface $adNetwork);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function getAdTagsForAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

    public function getAdTagsForAdNetworkAndSite(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null);

    /**
     * Saves the ad tag position bypassing doctrine flush and events (useful for bulk updates)
     *
     * @param AdTagInterface $adTag
     */
    public function saveAdTagPosition(AdTagInterface $adTag);
}