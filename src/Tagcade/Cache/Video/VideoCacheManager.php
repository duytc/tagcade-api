<?php
namespace Tagcade\Cache\Video;

use Tagcade\Cache\Video\Refresher\VideoWaterfallTagCacheRefresherInterface;
use Tagcade\DomainManager\VideoWaterfallTagManagerInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;

class VideoCacheManager implements VideoCacheMangerInterface
{
    /**
     * @var VideoWaterfallTagCacheRefresherInterface
     */
    private $videoWaterfallTagCacheRefresher;
    /**
     * @var
     */
    private $videoWaterfallTagManager;

    /**
     * @param VideoWaterfallTagCacheRefresherInterface $videoWaterfallTagCacheRefresher
     * @param VideoWaterfallTagManagerInterface $videoWaterfallTagManager
     */
    function __construct(VideoWaterfallTagCacheRefresherInterface $videoWaterfallTagCacheRefresher, VideoWaterfallTagManagerInterface $videoWaterfallTagManager)
    {
        $this->videoWaterfallTagCacheRefresher = $videoWaterfallTagCacheRefresher;
        $this->videoWaterfallTagManager = $videoWaterfallTagManager;
    }

    /**
     * @param $videoWaterfallTagId
     * @return mixed|void
     */
    public function refreshCacheForVideoWaterfallTag($videoWaterfallTagId)
    {
        $videoWaterfallTag = $this->videoWaterfallTagManager->find($videoWaterfallTagId);
        if (!$videoWaterfallTag instanceof VideoWaterfallTagInterface) {
            return;
        }

        $this->videoWaterfallTagCacheRefresher->refreshVideoWaterfallTag($videoWaterfallTag);
    }

    public function removeVideoWaterfallTagCache(array $videoWaterfallTagIds)
    {
        foreach ($videoWaterfallTagIds as $videoWaterfallTagId) {
            $videoWaterfallTag = $this->videoWaterfallTagManager->find($videoWaterfallTagId);
            if (!$videoWaterfallTag instanceof VideoWaterfallTagInterface) {
                continue;
            }

            $this->videoWaterfallTagCacheRefresher->removeVideoWaterfallTagCache($videoWaterfallTag);
        }
    }

    /**
     * @param $videoWaterfallTagId
     * @return mixed|void
     */
    public function getCacheForVideoWaterfallTag($videoWaterfallTagId)
    {
        $this->videoWaterfallTagCacheRefresher->getCacheForVideoWaterfallTag($videoWaterfallTagId);
    }
}