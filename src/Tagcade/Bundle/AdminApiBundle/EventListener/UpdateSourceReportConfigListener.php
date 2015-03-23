<?php

namespace Tagcade\Bundle\AdminApiBundle\EventListener;

use Tagcade\Bundle\AdminApiBundle\DomainManager\SourceReportEmailConfigManagerInterface;
use Tagcade\Bundle\AdminApiBundle\DomainManager\SourceReportSiteConfigManagerInterface;
use Tagcade\Bundle\AdminApiBundle\Event\NewSourceConfigEvent;
use Tagcade\Bundle\AdminApiBundle\Event\UpdateSourceConfigEvent;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportSiteConfigInterface;
use Tagcade\Model\Core\SiteInterface;

class UpdateSourceReportConfigListener
{
    /**
     * @var SourceReportEmailConfigManagerInterface
     */
    protected $sourceReportEmailConfigManager;

    /**
     * @var SourceReportSiteConfigManagerInterface
     */
    protected $sourceReportSiteConfigManager;

    /**
     * __construct
     *
     * @param SourceReportEmailConfigManagerInterface $sourceReportEmailConfigManager
     * @param SourceReportSiteConfigManagerInterface $sourceReportSiteConfigManager
     */
    function __construct(SourceReportEmailConfigManagerInterface $sourceReportEmailConfigManager, SourceReportSiteConfigManagerInterface $sourceReportSiteConfigManager)
    {
        $this->sourceReportEmailConfigManager = $sourceReportEmailConfigManager;
        $this->sourceReportSiteConfigManager = $sourceReportSiteConfigManager;
    }

    /**
     * handle event postPersist one site, this auto add site to SourceReportSiteConfig & SourceReportEmailConfig.
     *
     * @param NewSourceConfigEvent $event
     */
    public function onNewSiteThatEnableSourceReportInserted(NewSourceConfigEvent $event)
    {
        $this->sourceReportEmailConfigManager->saveSourceReportConfig($event->getEmails(), $event->getSites());
    }

    /**
     * handle event postUpdate one site, this auto remove SourceReportSiteConfigs if site's enableSourceReport change from true to false.
     *
     * @param UpdateSourceConfigEvent $event
     */
    public function onSiteThatEnableSourceReportUpdated(UpdateSourceConfigEvent $event)
    {
            /**
             * @var SiteInterface $site
             */
            $site = $event->getSite();

            if ($site->getEnableSourceReport()) {
                $email = $site->getPublisher()->getEmail();
                $this->sourceReportEmailConfigManager->saveSourceReportConfig(array($email), array($site));
            }
            else{
                $siteConfigs = $site->getSourceReportSiteConfigs();

                foreach ($siteConfigs as $siteConfig) {
                    /**
                     * @var SourceReportSiteConfigInterface $siteConfig
                     */
                    $this->sourceReportSiteConfigManager->delete($siteConfig);
                }
            }

    }


} 