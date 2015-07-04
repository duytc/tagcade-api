<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector;

use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\DomainManager\AdNetworkManagerInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformReportTypes;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork as AdNetworkReportTypes;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ExpandedReportCollection;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportResultInterface;

class ReportBuilder implements ReportBuilderInterface
{
    const PARAM_START_DATE = 'startDate';
    const PARAM_END_DATE = 'endDate';
    const PARAM_EXPAND = 'expand';
    const PARAM_GROUP = 'group';

    /**
     * @var ReportSelectorInterface
     */
    protected $reportSelector;
    /**
     * @var DateUtilInterface
     */
    protected $dateUtil;
    /**
     * @var PublisherManagerInterface
     */
    protected $userManager;
    /**
     * @var AdNetworkManagerInterface
     */
    protected $adNetworkManager;
    /**
     * @var SiteManagerInterface
     */
    protected $siteManager;
    /**
     * @var AdSlotManagerInterface
     */
    protected $adSlotManager;
    /**
     * @var AdTagManagerInterface
     */
    protected $adTagManager;

    public function __construct(
        ReportSelectorInterface $reportSelector,
        DateUtilInterface $dateUtil,
        PublisherManagerInterface $userManager,
        AdNetworkManagerInterface $adNetworkManager,
        SiteManagerInterface $siteManager,
        AdSlotManagerInterface $adSlotManager,
        AdTagManagerInterface $adTagManager
    )
    {
        $this->reportSelector = $reportSelector;
        $this->dateUtil = $dateUtil;
        $this->userManager = $userManager;
        $this->adNetworkManager = $adNetworkManager;
        $this->siteManager = $siteManager;
        $this->adSlotManager = $adSlotManager;
        $this->adTagManager = $adTagManager;
    }

    public function getPlatformReport(Params $params)
    {
        $publishers = $this->userManager->allPublishers();

        return $this->getReports(new PlatformReportTypes\Platform($publishers), $params);
    }

    public function getAllPublishersReport(Params $params)
    {
        $publishers = $this->userManager->allPublishers();

        $reportTypes = array_map(function(PublisherInterface $publisher) {
            return new PlatformReportTypes\Account($publisher);
        }, $publishers);

        return $this->getReports($reportTypes, $params);
    }

    public function getPublisherReport(PublisherInterface $publisher, Params $params)
    {
        return $this->getReports(new PlatformReportTypes\Account($publisher), $params);
    }

    public function getPublisherAdNetworksReport(PublisherInterface $publisher, Params $params)
    {
        $adNetworks = $this->adNetworkManager->getAdNetworksForPublisher($publisher);

        $reportTypes = array_map(function($adNetwork) {
            return new AdNetworkReportTypes\AdNetwork($adNetwork);
        }, $adNetworks);

        return $this->getReports($reportTypes, $params);
    }

    public function getAdNetworkReport(AdNetworkInterface $adNetwork, Params $params)
    {
        return $this->getReports(new AdNetworkReportTypes\AdNetwork($adNetwork), $params);
    }

    public function getAdnetworkSitesReport(AdNetworkInterface $adNetwork, Params $params)
    {
        $sites = $this->siteManager->getSitesThatHaveAdTagsBelongingToAdNetwork($adNetwork);

        $reportTypes = array_map(function($site) use($adNetwork) {
            return new AdNetworkReportTypes\Site($site, $adNetwork);
        }, $sites);

        return $this->getReports($reportTypes, $params);
    }

    public function getAdNetworkSiteReport(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params)
    {
        return $this->getReports(new AdNetworkReportTypes\Site($site, $adNetwork), $params);
    }

    public function getAdNetworkAdTagsReport(AdNetworkInterface $adNetwork, Params $params)
    {
        $adTags = $this->adTagManager->getAdTagsForAdNetwork($adNetwork);

        $reportTypes = array_map(function($adTag) {
            return new AdNetworkReportTypes\AdTag($adTag);
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    public function getAdNetworkSiteAdTagsReport(AdNetworkInterface $adNetwork, Siteinterface $site, Params $params)
    {
        $adTags = $this->adTagManager->getAdTagsForAdNetworkAndSite($adNetwork, $site);

        $reportTypes = array_map(function($adTag) {
            return new AdNetworkReportTypes\AdTag($adTag);
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    public function getAllSitesReport(Params $params)
    {
        $publishers = $this->siteManager->all();

        $reportTypes = array_map(function(SiteInterface $site) {
            return new PlatformReportTypes\Site($site);
        }, $publishers);

        return $this->getReports($reportTypes, $params);
    }

    public function getPublisherSitesReport(PublisherInterface $publisher, Params $params)
    {
        $sites = $this->siteManager->getSitesForPublisher($publisher);

        $reportTypes = array_map(function($site) {
            return new PlatformReportTypes\Site($site);
        }, $sites);

        return $this->getReports($reportTypes, $params);
    }

    public function getSiteReport(SiteInterface $site, Params $params)
    {
        return $this->getReports(new PlatformReportTypes\Site($site), $params);
    }

    public function getSiteAdNetworksReport(SiteInterface $site, Params $params)
    {
        $adNetworks = $this->getAdNetworksForSite($site);

        $reportTypes = array_map(
            function ($adNetwork) use($site) {
                return new AdNetworkReportTypes\Site($site, $adNetwork);
            },
            $adNetworks
        );

        return $this->getReports($reportTypes, $params);
    }


    public function getSiteAdSlotsReport(SiteInterface $site, Params $params)
    {
        $adSlots = $site->getReportableAdSlots();

        $reportTypes = array_map(function($adSlot) {
            return new PlatformReportTypes\AdSlot($adSlot);
        }, $adSlots);

        return $this->getReports($reportTypes, $params);
    }

    public function getSiteAdTagsReport(SiteInterface $site, Params $params)
    {
        $siteReport = $this->getSiteReport($site, $params);

        if (!$siteReport) {
            return false;
        }

        $adTags = $this->adTagManager->getAdTagsForSite($site);

        $reportTypes = array_map(function($adTag) {
            return new PlatformReportTypes\AdTag($adTag);
        }, $adTags);

        $adTagReports = $this->getReports($reportTypes, $params);

        if (!$adTagReports) {
            return false;
        }

        return new ExpandedReportCollection($adTagReports, $siteReport);
    }

    public function getPublisherAdSlotsReport(PublisherInterface $publisher, Params $params)
    {
        $adSlots = $this->adSlotManager->getReportableAdSlotsForPublisher($publisher);

        $reportTypes = array_map(function($adSlot) {
            return new PlatformReportTypes\AdSlot($adSlot);
        }, $adSlots);

        return $this->getReports($reportTypes, $params);
    }

    public function getAdSlotReport(ReportableAdSlotInterface $adSlot, Params $params)
    {
        return $this->getReports(new PlatformReportTypes\AdSlot($adSlot), $params);
    }

    public function getAdSlotAdTagsReport(ReportableAdSlotInterface $adSlot, Params $params)
    {
        $adSlotReport = $this->getAdSlotReport($adSlot, $params);

        if (!$adSlotReport) {
            return false;
        }

        $adTags = $adSlot->getAdTags()->toArray();

        $reportTypes = array_map(function($adTag) {
            return new PlatformReportTypes\AdTag($adTag);
        }, $adTags);

        $adTagReports = $this->getReports($reportTypes, $params);

        if (!$adTagReports) {
            return false;
        }

        return new ExpandedReportCollection($adTagReports, $adSlotReport);
    }

    public function getAdTagReport(AdTagInterface $adTag, Params $params)
    {
        return $this->getReports(new PlatformReportTypes\AdTag($adTag), $params);
    }

    /**
     * @param ReportTypeInterface|ReportTypeInterface[] $reportType
     * @param Params $params
     * @return ReportResultInterface|false
     */
    protected function getReports($reportType, Params $params)
    {
        if (is_array($reportType)) {
            return $this->reportSelector->getMultipleReports($reportType, $params);
        }

        return $this->reportSelector->getReports($reportType, $params);
    }

    /**
     * @param SiteInterface $site
     * @return AdNetworkInterface[]
     * @todo this should be a method of the AdNetworkManager
     */
    protected function getAdNetworksForSite(SiteInterface $site)
    {
        $adNetworks = [];

        $adTags = $this->adTagManager->getAdTagsForSite($site);

        foreach ($adTags as $adTag) {
            if (!in_array($adTag->getAdNetwork(), $adNetworks, $strict = true)) {
                $adNetworks[] = $adTag->getAdNetwork();
            }
        }

        return $adNetworks;
    }
}