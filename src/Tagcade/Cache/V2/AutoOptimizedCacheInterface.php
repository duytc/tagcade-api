<?php

namespace Tagcade\Cache\V2;
use Tagcade\Domain\DTO\Core\AutoOptimizeCacheParam;
use Tagcade\Model\Core\DisplayAdSlotInterface;

interface AutoOptimizedCacheInterface
{
    /**
     * get Existing Ad Slot Cache
     * TODO: move to a trait or TagCache
     *
     * @param $slotId
     * @return mixed
     */
    public function getExistingAdSlotCache($slotId);

    /**
     * get Optimized AdTag Positions For AdSlot. This is for UI displays in Ad Tag Manager of Ad Slot
     *
     * @param int $adSlotId
     * @param string $countryValue
     * @param string $domainValue
     * @param string $browserValue
     * @return array
     */
    public function getOptimizedAdTagPositionsForAdSlotBySegmentsValue($adSlotId, $countryValue = '', $domainValue = '', $browserValue = '');

    /**
     * update AutoOptimized Data For Ad Slot Cache
     *
     * @param DisplayAdSlotInterface $adSlot
     * @param array $autoOptimizedConfig
     * @return mixed
     */
    public function updateAutoOptimizedDataForAdSlotCache(DisplayAdSlotInterface $adSlot, array $autoOptimizedConfig);


    /**
     * @param $slotId
     * @param array $autoOptimizedConfig
     * @return mixed
     */
    public function updateAutoOptimizedConfigForAdSlot($slotId, array $autoOptimizedConfig);

    /**
     * @param AutoOptimizeCacheParam $param
     * @return mixed
     */
    public function updateCacheForAdSlots(AutoOptimizeCacheParam $param);

    /**
     * update Ad Slot Cache when AutoOptimizeIntegration is paused
     * -> remove autoOptimize key
     *
     * @param DisplayAdSlotInterface $adSlot
     * @return mixed
     */
    public function updateAdSlotCacheWhenAutoOptimizeIntegrationPaused(DisplayAdSlotInterface $adSlot);

    /**
     * @param AutoOptimizeCacheParam $param
     * @return mixed|void
     */
    public function getPreviewPositionForAdSlots(AutoOptimizeCacheParam $param);
}