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
        $videoWaterfallTags = $param->videoWaterfallTags;

        if (!is_array($videoWaterfallTags)) {
            throw new InvalidArgumentException(sprintf('Video ad tag expected an array, got type %s', gettype($videoWaterfallTags)));
        }

        /** @var VideoWaterfallTag $videoWaterfallTag */
        foreach ($videoWaterfallTags as $videoWaterfallTag) {
            $this->videoCacheManger->refreshCacheForVideoWaterfallTag($videoWaterfallTag);
        }
    }
}