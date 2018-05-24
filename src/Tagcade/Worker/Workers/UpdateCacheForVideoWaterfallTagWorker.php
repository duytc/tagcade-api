<?php


namespace Tagcade\Worker\Workers;


use InvalidArgumentException;
use stdClass;
use Tagcade\Cache\Video\VideoCacheMangerInterface;
use Tagcade\Entity\Core\VideoWaterfallTag;

class UpdateCacheForVideoWaterfallTagWorker
{
    /**
     * @var VideoCacheMangerInterface
     */
    private $videoCacheManger;

    function __construct(VideoCacheMangerInterface $videoCacheManger)
    {
        $this->videoCacheManger = $videoCacheManger;
    }

    /**
     * @param stdClass $param
     */
    public function updateCacheForVideoWaterfallTag(StdClass $param)
    {
        $videoWaterfallTagIds = $param->videoWaterfallTags;

        if (!is_array($videoWaterfallTagIds)) {
            throw new InvalidArgumentException(sprintf('Video ad tag expected an array, got type %s', gettype($videoWaterfallTagIds)));
        }

        foreach ($videoWaterfallTagIds as $videoWaterfallTagId) {
            $this->videoCacheManger->refreshCacheForVideoWaterfallTag($videoWaterfallTagId);
        }
    }
}