<?php

namespace Tagcade\Cache\V2;


use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;

interface TagCacheV2Interface
{
    /**
     * refreshCacheForDynamicAdSlot
     *
     * @param DynamicAdSlotInterface $dynamicAdSlot
     * @return mixed
     */
    public function refreshCacheForDynamicAdSlot(DynamicAdSlotInterface $dynamicAdSlot);

    /**
     * refreshCacheForNativeAdSlot
     *
     * @param NativeAdSlotInterface $nativeAdSlot
     * @param bool $alsoRefreshRelatedDynamicAdSlot
     * @return mixed
     */
    public function refreshCacheForNativeAdSlot(NativeAdSlotInterface $nativeAdSlot, $alsoRefreshRelatedDynamicAdSlot = true);

    /**
     * refreshCacheForReportableAdSlot
     *
     * @param ReportableAdSlotInterface $adSlot
     * @param bool $alsoRefreshRelatedDynamicAdSlot
     * @return mixed
     */
    public function refreshCacheForReportableAdSlot(ReportableAdSlotInterface $adSlot, $alsoRefreshRelatedDynamicAdSlot = true);

    /**
     * @param DisplayAdSlotInterface $adSlot
     * @param bool $alsoRefreshRelatedDynamicAdSlot
     * @return mixed
     */
    public function refreshCacheForDisplayAdSlot(DisplayAdSlotInterface $adSlot, $alsoRefreshRelatedDynamicAdSlot = true);

    /**
     * @param DisplayAdSlotInterface $adSlot
     * @param array $cacheKeys
     * @param bool $alsoRefreshRelatedDynamicAdSlot
     * @return mixed
     */
    public function removeKeysInSlotCacheForDisplayAdSlot(DisplayAdSlotInterface $adSlot, array $cacheKeys, $alsoRefreshRelatedDynamicAdSlot = true);

    /**
     * @param $adSlotId
     * @return mixed
     */
    public function removeCacheForAdSlot($adSlotId);

    /**
     * refreshCacheForRonAdSlot
     *
     * @param RonAdSlotInterface $ronAdSlot
     * @param bool $alsoRefreshRelatedDynamicRonAdSlot
     * @return mixed
     */
    public function refreshCacheForRonAdSlot(RonAdSlotInterface $ronAdSlot, $alsoRefreshRelatedDynamicRonAdSlot = true);

    /**
     * @param int $adSlotId
     * @return array
     */
    public function getAdTagsForAdSlot($adSlotId);

    /**
     * @param $ronAdSlotId
     * @return array
     */
    public function getAdTagsForRonAdSlot($ronAdSlotId);
}