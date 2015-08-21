<?php

namespace Tagcade\Bundle\AdminApiBundle\Service;

use Tagcade\Bundle\AdminApiBundle\DomainManager\SourceReportEmailConfigManagerInterface;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportEmailConfigInterface;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportSiteConfigInterface;
use Tagcade\DomainManager\SiteManagerInterface;

class SourceReportConfigService implements SourceReportConfigServiceInterface
{
    /**
     * @var SourceReportEmailConfigManagerInterface
     */
    private $sourceReportEmailConfigManager;

    /**
     * @var SiteManagerInterface
     */
    private $siteManager;

    function __construct(SourceReportEmailConfigManagerInterface $sourceReportEmailConfigManager, SiteManagerInterface $siteManager)
    {
        $this->sourceReportEmailConfigManager = $sourceReportEmailConfigManager;
        $this->siteManager = $siteManager;
    }

    /**
     * @inheritdoc
     */
    public function getAllSourceReportConfig()
    {
        //step 1. get all sites that has "enableSourceReport = true"
        $sites = $this->siteManager->getAllSitesThatEnableSourceReport();

        //step 2. get all active emailConfigs
        $emailConfigs = $this->sourceReportEmailConfigManager->getActiveConfig();

        //step 1. build all "reports" for output
        $reports = [];

        foreach ($sites as $site) {

            $filteredDomain = $this->formatSiteUrl($site->getDomain());
            $itemObject = [
                'domain' => $filteredDomain,
                'username' => $site->getPublisher()->getUser()->getUsername(),
                'pub_id' => $site->getPublisherId(),
                'site_id' => $site->getId(),
            ];

            $reports[$filteredDomain] = $itemObject;
        }

        //step 2. build all recipients for output
        $recipients = [];

        foreach ($emailConfigs as $emailConfig) {
            /**
             * @var SourceReportEmailConfigInterface $emailConfig
             */
            $siteConfigs = $emailConfig->getSourceReportSiteConfigs()->toArray();
            $sitesForThisEmailConfig = array_map(function (SourceReportSiteConfigInterface $siteConfig) {
                    return $this->formatSiteUrl($siteConfig->getSite()->getDomain());
                },
                (null === $siteConfigs || !is_array($siteConfigs)) ? [] : $siteConfigs
            );

            $reportsForThisEmailConfig = $emailConfig->getIncludedAll() ? ["*"] : $sitesForThisEmailConfig;

            // only add reports for email if $reportsForThisEmailConfig is not null
            if (null !== $reportsForThisEmailConfig && sizeof($reportsForThisEmailConfig) > 0) {
                $recipientItem = [
                    'email' => $emailConfig->getEmail(),
                    'reports' => $reportsForThisEmailConfig
                ];

                $recipients[] = $recipientItem;
            }
        }

        //step 3. return json result
        return [
            'reports' => $reports,
            'recipients' => $recipients,
        ];
    }

    /**
     * format SiteUrl
     *
     * @param string $siteUrl
     *
     * @return string
     */
    private function formatSiteUrl($siteUrl)
    {
        $fqdn = preg_replace('#(https?://)?(www\.)?([^/]+)/?.*$#', '$3', $siteUrl);
        return strtolower($fqdn);
    }
}