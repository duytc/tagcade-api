<?php

namespace Tagcade\Cache;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface TagCacheManagerInterface
{
    /**
     * @param DisplayAdSlotInterface $adSlot
     * @param $version
     * @return $this
     */
    public function refreshCacheForDisplayAdSlot(DisplayAdSlotInterface $adSlot, $version = 'All');

    /**
     * @param AdNetworkInterface $adNetwork
     * @param $version
     * @return $this
     */
    public function refreshCacheForAdNetwork(AdNetworkInterface $adNetwork, $version = 'All');

    /**
     * refresh cache for a/all publisher(s) with version
     * 
     * @param null|PublisherInterface $publisher null if refresh all publisher, default = null
     * @param $version, default = All for all version
     * @return $this
     */
    public function refreshCache($publisher = null, $version = 'All');

    public function refreshCacheForDynamicAdSlot(DynamicAdSlotInterface $dynamicAdSlot);

    public function refreshCacheForAdSlot(BaseAdSlotInterface $adSlot);

    public function refreshCacheForNativeAdSlot(NativeAdSlotInterface $nativeAdSlot);

    public function refreshCacheForRonAdSlot(RonAdSlotInterface $ronAdSlot, $alsoRefreshRelatedDynamicRonAdSlot = true);

}