<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\Core\ChannelSite as ChannelSiteModel;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\AdNetwork\SiteInterface;

class ChannelSite extends ChannelSiteModel
{
    protected $id;
    protected $channel;
    protected $site;
    protected $deletedAt;

    public function __construct()
    {
    }
}