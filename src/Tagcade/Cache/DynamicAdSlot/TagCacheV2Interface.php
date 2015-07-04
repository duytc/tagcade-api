<?php

namespace Tagcade\Cache\DynamicAdSlot;


use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;

interface TagCacheV2Interface {

    public function refreshCacheForDynamicAdSlot(DynamicAdSlotInterface $dynamicAdSlot);

    public function refreshCacheForNativeAdSlot(NativeAdSlotInterface $nativeAdSlot,  $alsoRefreshRelatedDynamicAdSlot = true);

    public function refreshCacheForReportableAdSlot(ReportableAdSlotInterface $adSlot, $alsoRefreshRelatedDynamicAdSlot = true);


} 