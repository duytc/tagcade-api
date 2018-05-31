<?php

namespace Tagcade\Service\Report\VideoReport\Counter;

interface SnapshotVideoCacheEventCounterInterface
{
    /**
     * @param $videoDemandAdTagId
     * @param $postFix
     * @return mixed
     */
    public function snapshotDemandAdTag($videoDemandAdTagId, $postFix);

    /**
     * @param $videoWaterfallTagId
     * @param $postFix
     * @return mixed
     */
    public function snapshotWaterfallTag($videoWaterfallTagId, $postFix);
}