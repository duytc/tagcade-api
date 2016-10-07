<?php


namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\VideoWaterfallTag as VideoWaterfallTagModel;

class VideoWaterfallTag extends VideoWaterfallTagModel
{
    protected $id;

    protected $uuid;
    protected $platform;
    protected $adDuration;
    protected $companionAds;
    protected $videoPublisher;
    protected $name;
    protected $videoWaterfallTagItems;
    protected $deletedAt;
    protected $buyPrice;
    protected $targeting;
}