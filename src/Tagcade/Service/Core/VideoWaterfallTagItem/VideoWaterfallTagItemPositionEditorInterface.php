<?php

namespace Tagcade\Service\Core\VideoWaterfallTagItem;

use Tagcade\Model\Core\VideoWaterfallTagInterface;

interface VideoWaterfallTagItemPositionEditorInterface
{
    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @param array $newOrderedVideoWaterfallTagItems format as:
     * [
     *      "videoWaterfallTagItems":[
     *          {
     *              "videoWaterfallTagItem":11,
     *              "videoDemandAdTags":[13]
     *          },
     *          {
     *              "videoWaterfallTagItem":12,
     *              "videoDemandAdTags":[14,15]
     *          },
     *          ...
     *      ]
     * ]
     * @return \Tagcade\Model\Core\VideoWaterfallTagItemInterface[]
     */
    public function setVideoWaterfallTagItemPositionForVideoWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag, array $newOrderedVideoWaterfallTagItems);
}