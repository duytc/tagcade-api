<?php

namespace Tagcade\Domain\DTO\Core;


use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;

class WaterfallTagStatus
{
    /**
     * @var VideoWaterfallTagInterface
     */
    private $waterfallTag;

    private $activeAdTagsCount;

    private $pausedAdTagsCount;

    function __construct(VideoWaterfallTagInterface $waterfallTag, VideoDemandPartnerInterface $videoDemandPartner)
    {
        $this->waterfallTag = $waterfallTag;

        $videoDemandAdTags = $videoDemandPartner->getVideoDemandAdTags();
        $this->pausedAdTagsCount = count(
            array_filter(
                $videoDemandAdTags,
                function(VideoDemandAdTagInterface $videoDemandAdTag) use ($waterfallTag)
                {
                    return $videoDemandAdTag->getVideoWaterfallTagItem()->getVideoWaterfallTag()->getId() === $waterfallTag->getId() && $videoDemandAdTag->getActive() === false;
                }
            )
        );

        $this->activeAdTagsCount = count(
            array_filter(
                $videoDemandAdTags,
                function(VideoDemandAdTagInterface $videoDemandAdTag) use ($waterfallTag)
                {
                    return $videoDemandAdTag->getVideoWaterfallTagItem()->getVideoWaterfallTag()->getId() === $waterfallTag->getId() && $videoDemandAdTag->getActive() === true;
                }
            )
        );

    }

    /**
     * @return VideoWaterfallTagInterface
     */
    public function getWaterfallTag()
    {
        return $this->waterfallTag;
    }

    /**
     * @return mixed
     */
    public function getActiveAdTagsCount()
    {
        return $this->activeAdTagsCount;
    }

    /**
     * @return mixed
     */
    public function getPausedAdTagsCount()
    {
        return $this->pausedAdTagsCount;
    }
}