<?php


namespace Tagcade\Cache\Video\Refresher;


use Tagcade\Model\Core\VideoWaterfallTagInterface;

interface VideoWaterfallTagCacheRefresherInterface
{
    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @return mixed
     */
    public function createCacheVideoWaterfallTagData(VideoWaterfallTagInterface $videoWaterfallTag);

    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @param array $extraData
     * @return mixed
     */
    public function refreshVideoWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag, $extraData = []);

    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @param array $cacheKeys
     * @return mixed
     */
    public function removeKeysInVideoWaterfallTagCacheForVideoWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag, array $cacheKeys);

    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @return mixed
     */
    public function removeVideoWaterfallTagCache(VideoWaterfallTagInterface $videoWaterfallTag);

    /**
     * @param $videoWaterfallTagId
     * @return mixed
     */
    public function getCacheForVideoWaterfallTag($videoWaterfallTagId);
}