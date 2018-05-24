<?php

namespace Tagcade\Worker\Workers;

use stdClass;
use Tagcade\Cache\Video\Refresher\VideoWaterfallTagCacheRefresherInterface;
use Tagcade\DomainManager\VideoWaterfallTagManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\VideoWaterfallTagInterface;

// responsible for doing the background tasks assigned by the manager
// all public methods on the class represent tasks that can be done

class UpdateVideoWaterfallTagCacheWhenRemoveOptimizationIntegrationWorker
{
    /** @var VideoWaterfallTagManagerInterface */
    private $videoWaterfallTagManager;

    /** @var VideoWaterfallTagCacheRefresherInterface */
    private $videoWaterfallTagCacheRefresher;

    /**
     * UpdateVideoWaterfallTagCacheWhenRemoveOptimizationIntegrationWorker constructor.
     *
     * @param VideoWaterfallTagManagerInterface $videoWaterfallTagManager
     * @param VideoWaterfallTagCacheRefresherInterface $videoWaterfallTagCacheRefresher
     */
    public function __construct(VideoWaterfallTagManagerInterface $videoWaterfallTagManager, VideoWaterfallTagCacheRefresherInterface $videoWaterfallTagCacheRefresher)
    {
        $this->videoWaterfallTagManager = $videoWaterfallTagManager;
        $this->videoWaterfallTagCacheRefresher = $videoWaterfallTagCacheRefresher;
    }

    /**
     * update cache for multiple Video Waterfall Tag when optimization integration removed
     *
     * @param StdClass $params
     */
    public function updateVideoWaterfallTagCacheWhenRemoveOptimizationIntegration(stdClass $params)
    {
        $videoWaterfallTagIds = $params->videoWaterfallTags;
        if (!is_array($videoWaterfallTagIds) || empty($videoWaterfallTagIds)) {
            return;
        }

        foreach ($videoWaterfallTagIds as $videoWaterfallTagId) {
            $videoWaterfallTag = $this->videoWaterfallTagManager->find($videoWaterfallTagId);

            if (!$videoWaterfallTag instanceof VideoWaterfallTagInterface) {
                throw new InvalidArgumentException(sprintf('Video Waterfall Tag %d does not exist', $videoWaterfallTagId));
            }

            $cacheKeysToBeRemoved = ['autoOptimize'];
            $this->videoWaterfallTagCacheRefresher->removeKeysInVideoWaterfallTagCacheForVideoWaterfallTag($videoWaterfallTag, $cacheKeysToBeRemoved);
        }
    }
}