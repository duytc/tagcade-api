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
    protected $enableSourceReport;
    protected $sourceReportSiteConfigs;

    public function __construct()
    {}
}