<?php

namespace Tagcade\Cache;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface TagCacheInterface
{
    /**
     * @param DisplayAdSlotInterface $adSlot
     * @return $this
     */
    public function refreshCacheForDisplayAdSlot(DisplayAdSlotInterface $adSlot);

    /**
     * @param DisplayAdSlotInterface $adSlot
     * @param array $cacheKeys
     * @return mixed
     */
    public function removeKeysInSlotCacheForDisplayAdSlot(DisplayAdSlotInterface $adSlot, array $cacheKeys);

    /**
     * @param $adSlotId
     * @return mixed
     */
    public function removeCacheForAdSlot($adSlotId);

    /**
     * @param AdNetworkInterface $adNetwork
     * @return $this
     */
    public function refreshCacheForAdNetwork(AdNetworkInterface $adNetwork);

    /**
     * refresh cache for a/all publisher(s)
     * 
     * @param null|PublisherInterface $publisher null if refresh all publisher, default = null
     * @return $this
     */
    public function refreshCache($publisher = null);

    public function supportVersion($version);
}