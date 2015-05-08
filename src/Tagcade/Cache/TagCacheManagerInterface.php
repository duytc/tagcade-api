<?php

namespace Tagcade\Cache;

use Tagcade\Legacy\TagCacheInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;

interface TagCacheManagerInterface
{
    /**
     * @param AdSlotInterface $adSlot
     * @param $version
     * @return $this
     */
    public function refreshCacheForAdSlot(AdSlotInterface $adSlot, $version = 'All');

    /**
     * @param AdNetworkInterface $adNetwork
     * @param $version
     * @return $this
     */
    public function refreshCacheForAdNetwork(AdNetworkInterface $adNetwork, $version = 'All');

    /**
     * @param $version
     * @return $this
     */
    public function refreshCache($version = 'All');

    public function refreshCacheForDynamicAdSlot(DynamicAdSlotInterface $dynamicAdSlot);

}