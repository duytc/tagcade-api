<?php

namespace Tagcade\Cache\Video;
use Tagcade\Domain\DTO\Core\Video\AutoOptimizeVideoCacheParam;
use Tagcade\Model\Core\VideoWaterfallTagInterface;

interface AutoOptimizedVideoCacheInterface
{
    /**
     * get Existing Ad Slot Cache
     * TODO: move to a trait or TagCache
     *
     * @param VideoWaterfallTagInterface $waterfallTag
     * @return mixed
     */
    public function getExistingWaterfallTagCache(VideoWaterfallTagInterface $waterfallTag);

    /**
     * * get Optimized DemandTag Positions For WaterfallTag. This is for UI displays in DemandAdTag Manager of WaterfallTag
     *
    * @param VideoWaterfallTagInterface $waterfallTag
    * @return array
    */
    public function getOptimizedDemandTagPositionsForWaterfallTag(VideoWaterfallTagInterface $waterfallTag);

    /**
     * update AutoOptimized Data For Ad Slot Cache
     *
     * @param VideoWaterfallTagInterface $waterfallTag
     * @param array $autoOptimizedConfig
     * @return mixed
     */
    public function updateAutoOptimizedDataForWaterfallTagCache(VideoWaterfallTagInterface $waterfallTag, array $autoOptimizedConfig);


    /**
     * @param $waterfallTagId
     * @param array $autoOptimizedConfig
     * @return mixed
     */
    public function updateAutoOptimizedVideoConfigForWaterfallTag($waterfallTagId, array $autoOptimizedConfig);

    /**
     * @param AutoOptimizeVideoCacheParam $param
     * @return mixed
     */
    public function updateCacheForWaterfallTags(AutoOptimizeVideoCacheParam $param);

    /**
     * update Ad Slot Cache when AutoOptimizeIntegration is paused
     * -> remove autoOptimize key
     *
     * @param VideoWaterfallTagInterface $waterfallTag
     * @return mixed
     */
    public function updateWaterfallTagCacheWhenAutoOptimizeIntegrationPaused(VideoWaterfallTagInterface $waterfallTag);

    /**
     * @param AutoOptimizeVideoCacheParam $param
     * @return mixed|void
     */
    public function getPreviewPositionForWaterfallTags(AutoOptimizeVideoCacheParam $param);
}