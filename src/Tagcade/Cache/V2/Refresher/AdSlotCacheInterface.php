<?php

namespace Tagcade\Cache\V2\Refresher;


use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AdSlotCacheInterface
{
    /**
     * refresh cache for a/all publisher(s)
     *
     * @param null|PublisherInterface $publisher null if refresh all publisher, default = null
     * @return mixed
     */
    public function refreshCache($publisher = null);

    /**
     * @param DisplayAdSlotInterface $adSlot
     * @param bool $alsoRefreshRelatedDynamicAdSlot
     * @return mixed
     */
    public function refreshCacheForDisplayAdSlot(DisplayAdSlotInterface $adSlot, $alsoRefreshRelatedDynamicAdSlot = true);

    /**
     * @param NativeAdSlotInterface $nativeAdSlot
     * @param bool $alsoRefreshRelatedDynamicAdSlot
     * @return mixed
     */
    public function refreshCacheForNativeAdSlot(NativeAdSlotInterface $nativeAdSlot, $alsoRefreshRelatedDynamicAdSlot = true);

    /**
     * @param DynamicAdSlotInterface $dynamicAdSlot
     * @return mixed
     */
    public function refreshCacheForDynamicAdSlot(DynamicAdSlotInterface $dynamicAdSlot);

    /**
     * @param $cacheKey
     * @param ModelInterface $model
     * @return mixed
     */
    public function refreshForCacheKey($cacheKey, ModelInterface $model);

    /**
     * remove the specify cache key
     * @param $cacheKey
     * @param ModelInterface $model
     * @return mixed
     */
    public function removeCacheKey($cacheKey, ModelInterface $model);

    /**
     * @param BaseAdSlotInterface $adSlot
     * @return mixed
     */
    public function createAdSlotCacheData(BaseAdSlotInterface $adSlot);

    /**
     * @param $slotId
     * @return mixed
     */
    public function getNamespace($slotId);

    /**
     * @param $slotId
     * @return string|false json string of ad slot cache
     */
    public function getAdTagsForAdSlot($slotId);
}