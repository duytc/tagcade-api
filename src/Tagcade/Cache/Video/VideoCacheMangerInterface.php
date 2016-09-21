<?php


namespace Tagcade\Cache\Video;



interface VideoCacheMangerInterface {

    /**
     * @param $videoWaterfallTagId
     * @return mixed
     */
    public function refreshCacheForVideoWaterfallTag($videoWaterfallTagId);

    /**
     * @param $videoWaterfallTagIds
     * @return mixed
     */
    public function removeVideoWaterfallTagCache(array $videoWaterfallTagIds);

    /**
     * @param $videoWaterfallTagId
     * @return mixed
     */
    public function getCacheForVideoWaterfallTag($videoWaterfallTagId);
} 