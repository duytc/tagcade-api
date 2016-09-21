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
     * @return mixed
     */
    public function refreshVideoWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag);

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