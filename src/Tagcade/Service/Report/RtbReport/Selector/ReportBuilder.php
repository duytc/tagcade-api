<?php

namespace Tagcade\Service\Report\RtbReport\Selector;

use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\DomainManager\AdNetworkManagerInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\RonAdSlotManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\Account;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\AdSlot;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\Platform;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\RonAdSlot;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\Site;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\RtbReport\Selector\Result\ExpandedReportCollection;
use Tagcade\Service\Report\RtbReport\Selector\Result\ReportResultInterface;

class ReportBuilder implements ReportBuilderInterface
{
    const PARAM_START_DATE = 'startDate';
    const PARAM_END_DATE = 'endDate';
    const PARAM_EXPAND = 'expand';
    const PARAM_GROUP = 'group';

    /** @var ReportSelectorInterface */
    protected $reportSelector;

    /** @var DateUtilInterface */
    protected $dateUtil;

    /** @var PublisherManagerInterface */
    protected $userManager;

    /** @var AdNetworkManagerInterface */
    protected $adNetworkManager;

    /** @var SiteManagerInterface */
    protected $siteManager;

    /** @var AdSlotManagerInterface */
    protected $adSlotManager;

    /** @var RonAdSlotManagerInterface */
    protected $ronAdSlotManager;

    public function __construct(
        ReportSelectorInterface $reportSelector,
        DateUtilInterface $dateUtil,
        PublisherManagerInterface $userManager,
        AdNetworkManagerInterface $adNetworkManager,
        SiteManagerInterface $siteManager,
        AdSlotManagerInterface $adSlotManager,
        RonAdSlotManagerInterface $ronAdSlotManager
    )
    {
        $this->reportSelector = $reportSelector;
        $this->dateUtil = $dateUtil;
        $this->userManager = $userManager;
        $this->adNetworkManager = $adNetworkManager;
        $this->siteManager = $siteManager;
        $this->adSlotManager = $adSlotManager;
        $this->ronAdSlotManager = $ronAdSlotManager;
    }

    public function getPlatformReport(Params $params)
    {
        $publishers = $this->userManager->allPublishers();

        return $this->getReports(new Platform($publishers), $params);
    }

    public function getAllPublishersReport(Params $params)
    {
        $publishers = $this->userManager->allActivePublishers();

        $reportTypes = array_map(function (PublisherInterface $publisher) {
            return new Account($publisher);
        }, $publishers);

        return $this->getReports($reportTypes, $params);
    }

    public function getPublisherReport(PublisherInterface $publisher, Params $params)
    {
        return $this->getReports(new Account($publisher), $params);
    }

    public function getPublisherDSPsReport(PublisherInterface $publisher, Params $params)
    {
        // TODO: Implement getPublisherDSPsReport() method.
    }

    public function getDSPReport(AdNetworkInterface $adNetwork, Params $params)
    {
        // TODO: Implement getDSPReport() method.
    }

    public function getDSPSitesReport(AdNetworkInterface $adNetwork, Params $params)
    {
        // TODO: Implement getDSPSitesReport() method.
    }

    public function getDSPSiteReport(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params)
    {
        // TODO: Implement getDSPSiteReport() method.
    }

    public function getAllSitesReport(Params $params)
    {
        $sites = $this->siteManager->all();

        $sites = array_filter($sites, function (SiteInterface $site) {
            return $site->isRTBEnabled();
        });

        $reportTypes = array_map(function (SiteInterface $site) {
            return new Site($site);
        }, $sites);

        return $this->getReports($reportTypes, $params);
    }

    public function getPublisherSitesReport(PublisherInterface $publisher, Params $params)
    {
        $sites = $this->siteManager->getSitesForPublisher($publisher);

        $sites = array_filter($sites, function (SiteInterface $site) {
            return $site->isRTBEnabled();
        });

        $reportTypes = array_map(function ($site) {
            return new Site($site);
        }, $sites);

        return $this->getReports($reportTypes, $params);
    }

    public function getSiteReport(SiteInterface $site, Params $params)
    {
        return $this->getReports(new Site($site), $params);
    }

    public function getSiteAdSlotsReport(SiteInterface $site, Params $params)
    {
        $adSlots = $site->getReportableAdSlots();

        $adSlots = array_filter($adSlots, function (BaseAdSlotInterface $adSlot) {
            return $adSlot->isRTBEnabled();
        });

        $reportTypes = array_map(function ($adSlot) {
            return new AdSlot($adSlot);
        }, $adSlots);

        return $this->getReports($reportTypes, $params);
    }

    public function getPublisherAdSlotsReport(PublisherInterface $publisher, Params $params)
    {
        $adSlots = $this->adSlotManager->getReportableAdSlotsForPublisher($publisher);

        $adSlots = array_filter($adSlots, function (BaseAdSlotInterface $adSlot) {
            return $adSlot->isRTBEnabled();
        });

        $reportTypes = array_map(function ($adSlot) {
            return new AdSlot($adSlot);
        }, $adSlots);

        return $this->getReports($reportTypes, $params);
    }

    public function getAdSlotReport(ReportableAdSlotInterface $adSlot, Params $params)
    {
        return $this->getReports(new AdSlot($adSlot), $params);
    }

    public function getRonAdSlotReport(RonAdSlotInterface $ronAdSlot, Params $params)
    {
        return $this->getReports(new RonAdSlot($ronAdSlot), $params);
    }

    public function getPublisherRonAdSlotReport(PublisherInterface $publisher, Params $params)
    {
        $ronAdSlots = $this->ronAdSlotManager->getRonAdSlotsForPublisher($publisher);

        $ronAdSlots = array_filter($ronAdSlots, function (RonAdSlotInterface $ronAdSlot) {
            return $ronAdSlot->isRTBEnabled();
        });

        $reportTypes = array_map(function ($ronAdSlot) {
            return new RonAdSlot($ronAdSlot);
        }, $ronAdSlots);

        return $this->getReports($reportTypes, $params);
    }

    public function getRonAdSlotSegmentReport(RonAdSlotInterface $ronAdSlot, Params $params)
    {
        $segments = $ronAdSlot->getSegments();

        $reportTypes = array_map(function ($segment) use ($ronAdSlot) {
            return new RonAdSlot($ronAdSlot, $segment);
        }, $segments);

        return $this->getReports($reportTypes, $params);
    }

    public function getRonAdSlotSiteReport(RonAdSlotInterface $ronAdSlot, Params $params)
    {
        $ronAdSlotReport = $this->getRonAdSlotReport($ronAdSlot, $params);

        if (!$ronAdSlotReport) {
            return false;
        }

        $adSlots = $this->adSlotManager->getAdSlotsForRonAdSlot($ronAdSlot);

        $reportTypes = array_map(function ($adSlot) {
            return new AdSlot($adSlot);
        }, $adSlots);

        $adSlotReports = $this->getReports($reportTypes, $params);

        if (!$adSlotReports) {
            return false;
        }

        return new ExpandedReportCollection($adSlotReports, $ronAdSlotReport);
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
}