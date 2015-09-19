<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\ChannelSiteInterface;
use Tagcade\Model\Core\Site as SiteModel;

class Site extends SiteModel
{
    protected $id;
    protected $publisher;
    protected $name;
    protected $domain;
    protected $deletedAt;
    protected $enableSourceReport;
    protected $sourceReportSiteConfigs;
    protected $channelSites;
    protected $players;

    public function __construct()
    {}
}