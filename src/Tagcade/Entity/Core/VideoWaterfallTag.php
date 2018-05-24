<?php


namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\IvtPixelWaterfallTagInterface;
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
    protected $autoOptimize;
    protected $optimizationIntegration;

    /* new feature: Server-Side VAST+VAPID, Server-Side VAST Only, Client-Side VAST+VAPID (default)*/
    protected $runOn;
    /** @var IvtPixelWaterfallTagInterface[] */
    protected $ivtPixelWaterfallTags;

    /**
     * call parent construct for default in form
     */
    function __construct()
    {
        parent::__construct();
    }
}