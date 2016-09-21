<?php


namespace Tagcade\Worker\Workers;


use InvalidArgumentException;
use stdClass;
use Tagcade\Cache\Video\VideoCacheMangerInterface;

class RemoveCacheForVideoWaterfallTagWorker
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
    public function removeCacheForVideoWaterfallTag(StdClass $param)
    {
        $videoWaterfallTags = $param->videoWaterfallTags;

        if (!is_array($videoWaterfallTags)) {
            throw new InvalidArgumentException(sprintf('Video ad tag expected an array, got type %s', gettype($videoWaterfallTags)));
        }

        $this->videoCacheManger->removeVideoWaterfallTagCache($videoWaterfallTags);
    }
}