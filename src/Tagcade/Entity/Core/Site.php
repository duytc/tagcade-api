<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\Site as SiteModel;

class Site extends SiteModel
{
    protected $id;
    protected $publisher;
    protected $name;
    protected $domain;
    protected $deletedAt;
    protected $rtbStatus;
    protected $rtb;
    protected $enableSourceReport;
    protected $sourceReportSiteConfigs;
    protected $channelSites;
    protected $players;
    protected $autoCreate;
    protected $exchanges;

    /**
     * this constructor will be called by FormType, must be used to call parent to set default values
     */
    public function __construct()
    {
        parent::__construct();
    }
}