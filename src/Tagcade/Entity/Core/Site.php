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
    protected $channelSites;
    protected $players;
    protected $autoCreate;
    protected $subPublisher;

    /**
     * this constructor will be called by FormType, must be used to call parent to set default values
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param array $fieldValues
     * @return Site
     */
    public static function createSiteFromArray(array $fieldValues)
    {
        $site = new self();
        foreach ($fieldValues as $nameField=>$value) {
            $method = sprintf('set%s',ucwords($nameField));
            $site->$method($value);
        }

        return $site;
    }
}