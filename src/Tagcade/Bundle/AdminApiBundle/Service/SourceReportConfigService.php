<?php

namespace Tagcade\Bundle\AdminApiBundle\Service;

use Doctrine\Common\Collections\Collection;
use Tagcade\Bundle\AdminApiBundle\DomainManager\SourceReportEmailConfigManagerInterface;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportEmailConfigInterface;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportSiteConfigInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Model\Core\SiteInterface;

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

        //step 3. build all "reports" for output
        //formatted as:
        // reports: [
        //      <filtered Domain of site 1>: {
        //          'domain' => <filtered Domain of site 1>,
        //          'username' => <username of publisher,
        //          'pub_id' => <publisherId of site>,
        //          'site_id' => <siteId>
        //      },
        //      <filtered Domain of site 2>: {
        //          'domain' => ..., 'username' => ..., 'pub_id' => ..., 'site_id' => ...
        //      },
        //      ...
        // ]
        $reports = [];

        foreach ($sites as $site) {
            $filteredDomain = $this->formatSiteUrl($site->getDomain());
            $itemObject = [
                'domain' => $filteredDomain,
                'username' => $site->getPublisher()->getUser()->getUsername(),
                'pub_id' => $site->getPublisherId(),
                'site_id' => $site->getId()
            ];

            $reports[$filteredDomain] = $itemObject;
        }

        //step 4. build all recipients for output
        // recipients: [
        //      0: {
        //          'email' => <email for sending reports>,
        //          'reports' => [reports_1, reports_2, ...]
        //      },
        //      ...1: [],...n: []
        // ]
        $recipients = [];

        foreach ($emailConfigs as $emailConfig) {
            /** @var SourceReportEmailConfigInterface $emailConfig */
            if ($emailConfig->getIncludedAll()) {
                // included all
                $reportsForThisEmailConfig = ["*"];
            } else if (is_array($emailConfig->getIncludedAllSitesOfPublishers())
                && sizeof($emailConfig->getIncludedAllSitesOfPublishers()) > 0
            ) {
                // included all sites of specific publishers
                $reportsForThisEmailConfig = $this->getReportsForIncludedAllSites($emailConfig->getIncludedAllSitesOfPublishers(), $sites);
            } else {
                // normal detail config
                $siteConfigs = $emailConfig->getSourceReportSiteConfigs();
                if ($siteConfigs instanceof Collection) {
                    $siteConfigs = $siteConfigs->toArray();
                }

                $reportsForThisEmailConfig = array_map(function (SourceReportSiteConfigInterface $siteConfig) {
                        return $this->formatSiteUrl($siteConfig->getSite()->getDomain());
                    },
                    (null === $siteConfigs || !is_array($siteConfigs)) ? [] : $siteConfigs
                );
            }

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

    /**
     * get Reports For Included All Sites
     * @param int[] $publisherIds specific publisher_ids include sites need be created reports
     * @param SiteInterface[] $sites all sites need be filtered for each publisher_id
     * @return array all report for each email
     */
    private function getReportsForIncludedAllSites(array $publisherIds, array $sites)
    {
        // make $sites is unique
        $sites = array_unique($sites);

        $reports = [];

        array_walk($sites,
            function ($site) use (&$reports, $publisherIds) {
                /** @var SiteInterface $site */
                if (!$site instanceof SiteInterface) {
                    return;
                }

                if (in_array($site->getPublisherId(), $publisherIds)) {
                    $reports[] = $this->formatSiteUrl($site->getDomain());
                }
            }
        );

        return $reports;
    }
}