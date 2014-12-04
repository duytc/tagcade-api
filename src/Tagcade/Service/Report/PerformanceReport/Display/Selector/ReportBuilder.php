<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector;

use Tagcade\Bundle\UserBundle\DomainManager\UserManagerInterface;
use Tagcade\DomainManager\AdNetworkManagerInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformReportTypes;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork as AdNetworkReportTypes;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\DateUtilInterface;
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
     * @var UserManagerInterface
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
    private $adSlotManager;


    public function __construct(
        ReportSelectorInterface $reportSelector,
        DateUtilInterface $dateUtil,
        UserManagerInterface $userManager,
        AdNetworkManagerInterface $adNetworkManager,
        SiteManagerInterface $siteManager,
        AdSlotManagerInterface $adSlotManager
    )
    {
        $this->reportSelector = $reportSelector;
        $this->dateUtil = $dateUtil;
        $this->userManager = $userManager;
        $this->adNetworkManager = $adNetworkManager;
        $this->siteManager = $siteManager;
        $this->adSlotManager = $adSlotManager;
    }

    /**
     * @inheritdoc
     */
    public function getPlatformReport(Params $params)
    {
        $publishers = $this->userManager->allPublisherRoles();

        return $this->getReports(new PlatformReportTypes\Platform($publishers), $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllPublishersReport(Params $params)
    {
        $publishers = $this->userManager->allPublisherRoles();

        $reportTypes = array_map(function(PublisherInterface $publisher) {
            return new PlatformReportTypes\Account($publisher);
        }, $publishers);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getPublisherReport(PublisherInterface $publisher, Params $params)
    {
        return $this->getReports(new PlatformReportTypes\Account($publisher), $params);
    }

    /**
     * @inheritdoc
     */
    public function getPublisherAdNetworksReport(PublisherInterface $publisher, Params $params)
    {
        $adNetworks = $this->adNetworkManager->getAdNetworksForPublisher($publisher);

        $reportTypes = array_map(function($adNetwork) {
            return new AdNetworkReportTypes\AdNetwork($adNetwork);
        }, $adNetworks);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAdNetworkReport(AdNetworkInterface $adNetwork, Params $params)
    {
        return $this->getReports(new AdNetworkReportTypes\AdNetwork($adNetwork), $params);
    }

    /**
     * @inheritdoc
     */
    public function getAdnetworkSitesReport(AdNetworkInterface $adNetwork, Params $params)
    {
        $sites = $this->siteManager->getSitesThatHaveAdTagsBelongingToAdNetwork($adNetwork);

        $reportTypes = array_map(function($site) use($adNetwork) {
            return new AdNetworkReportTypes\Site($site, $adNetwork);
        }, $sites);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAdNetworkSiteReport(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params)
    {
        return $this->getReports(new AdNetworkReportTypes\Site($site, $adNetwork), $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllSitesReport(Params $params)
    {
        $publishers = $this->siteManager->all();

        $reportTypes = array_map(function(SiteInterface $site) {
            return new PlatformReportTypes\Site($site);
        }, $publishers);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getPublisherSitesReport(PublisherInterface $publisher, Params $params)
    {
        $sites = $this->siteManager->getSitesForPublisher($publisher);

        $reportTypes = array_map(function($site) {
            return new PlatformReportTypes\Site($site);
        }, $sites);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getSiteReport(SiteInterface $site, Params $params)
    {
        return $this->getReports(new PlatformReportTypes\Site($site), $params);
    }

    /**
     * @inheritdoc
     */
    public function getSiteAdSlotsReport(SiteInterface $site, Params $params)
    {
        $adSlots = $site->getAdSlots()->toArray();

        $reportTypes = array_map(function($adSlot) {
            return new PlatformReportTypes\AdSlot($adSlot);
        }, $adSlots);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAdSlotReport(AdSlotInterface $adSlot, Params $params)
    {
        return $this->getReports(new PlatformReportTypes\AdSlot($adSlot), $params);
    }

    /**
     * @inheritdoc
     */
    public function getAdSlotAdTagsReport(AdSlotInterface $adSlot, Params $params)
    {
        $adTags = $adSlot->getAdTags()->toArray();

        $reportTypes = array_map(function($adTag) {
            return new PlatformReportTypes\AdTag($adTag);
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
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
     * @inheritdoc
     */
    public function getPublisherAdSlotsReport(PublisherInterface $publisher, Params $params)
    {
        $adSlots = $this->adSlotManager->getAdSlotsForPublisher($publisher);

        $reportTypes = array_map(function($adSlot) {
            return new PlatformReportTypes\AdSlot($adSlot);
        }, $adSlots);

        return $this->getReports($reportTypes, $params);
    }


}