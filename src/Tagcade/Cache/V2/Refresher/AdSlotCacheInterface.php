<?php

namespace Tagcade\Cache\V2\Refresher;


use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\ModelInterface;

interface AdSlotCacheInterface
{
    public function refreshCache();

    public function refreshCacheForDisplayAdSlot(DisplayAdSlotInterface $adSlot, $alsoRefreshRelatedDynamicAdSlot = true);

    public function refreshCacheForNativeAdSlot(NativeAdSlotInterface $nativeAdSlot, $alsoRefreshRelatedDynamicAdSlot = true);

    public function refreshCacheForDynamicAdSlot(DynamicAdSlotInterface $dynamicAdSlot);

    public function refreshForCacheKey($cacheKey, ModelInterface $model);

    public function createAdSlotCacheData(BaseAdSlotInterface $adSlot);

    public function getNamespace($slotId);

    /**
     * @param $slotId
     * @return string|false json string of ad slot cache
     */
    public function getAdTagsForAdSlot($slotId);
}