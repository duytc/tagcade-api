<?php

namespace Tagcade\Service\Report\VideoReport\Counter;

interface SnapshotVideoCacheEventCounterInterface
{
    /**
     * @param $videoDemandAdTagId
     * @param $postFix
     * @param bool $yesterdayOption
     * @return mixed
     */
    public function snapshotDemandAdTag($videoDemandAdTagId, $postFix,  $yesterdayOption = false);

    /**
     * @param $videoWaterfallTagId
     * @param $postFix
     * @param bool $yesterdayOption
     * @return mixed
     */
    public function snapshotWaterfallTag($videoWaterfallTagId, $postFix,  $yesterdayOption = false);
}