<?php


namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\VideoWaterfallTagItem as VideoWaterfallTagItemModel;

class VideoWaterfallTagItem extends VideoWaterfallTagItemModel
{
    protected $id;
    protected $position;
    protected $strategy;
    protected $videoWaterfallTag;
    protected $videoDemandAdTags;
    protected $deletedAt;
}