<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector;

use Tagcade\Bundle\UserBundle\DomainManager\UserManagerInterface;
use Tagcade\DomainManager\AdNetworkManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformReportTypes;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork as AdNetworkReportTypes;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\ReportCollection;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\ReportGroup;
use Tagcade\Service\DateUtilInterface;

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

    public function __construct(
        ReportSelectorInterface $reportSelector,
        DateUtilInterface $dateUtil,
        UserManagerInterface $userManager,
        AdNetworkManagerInterface $adNetworkManager,
        SiteManagerInterface $siteManager
    )
    {
        $this->reportSelector = $reportSelector;
        $this->dateUtil = $dateUtil;
        $this->userManager = $userManager;
        $this->adNetworkManager = $adNetworkManager;
        $this->siteManager = $siteManager;
    }

    /**
     * @inheritdoc
     */
    public function getPlatformReport(array $params = [])
    {
        $publishers = $this->userManager->allPublisherRoles();

        return $this->getReports(new PlatformReportTypes\Platform($publishers), $params);
    }

    /**
     * @inheritdoc
     */
    public function getPublishersReport(array $params = [])
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
    public function getPublisherReport(PublisherInterface $publisher, array $params = [])
    {
        return $this->getReports(new PlatformReportTypes\Account($publisher), $params);
    }

    /**
     * @inheritdoc
     */
    public function getPublisherAdNetworksReport(PublisherInterface $publisher, array $params = [])
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
    public function getAdNetworkReport(AdNetworkInterface $adNetwork, array $params = [])
    {
        return $this->getReports(new AdNetworkReportTypes\AdNetwork($adNetwork), $params);
    }

    /**
     * @inheritdoc
     */
    public function getAdnetworkSitesReport(AdNetworkInterface $adNetwork, array $params = [])
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
    public function getAdNetworkSiteReport(AdNetworkInterface $adNetwork, SiteInterface $site, array $params = [])
    {
        return $this->getReports(new AdNetworkReportTypes\Site($site, $adNetwork), $params);
    }

    /**
     * @inheritdoc
     */
    public function getPublisherSitesReport(PublisherInterface $publisher, array $params = [])
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
    public function getSiteReport(SiteInterface $site, array $params = [])
    {
        return $this->getReports(new PlatformReportTypes\Site($site), $params);
    }

    /**
     * @inheritdoc
     */
    public function getSiteAdSlotsReport(SiteInterface $site, array $params = [])
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
    public function getAdSlotReport(AdSlotInterface $adSlot, array $params = [])
    {
        return $this->getReports(new PlatformReportTypes\AdSlot($adSlot), $params);
    }

    /**
     * @inheritdoc
     */
    public function getAdSlotAdTagsReport(AdSlotInterface $adSlot, array $params = [])
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
    public function getAdTagReport(AdTagInterface $adTag, array $params = [])
    {
        return $this->getReports(new PlatformReportTypes\AdTag($adTag), $params);
    }

    /**
     * @param ReportTypeInterface|ReportTypeInterface[] $reportType
     * @param array $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    protected function getReports($reportType, array $params = [])
    {
        // create a params array with all values set to null
        $defaultParams = array_fill_keys([
            self::PARAM_START_DATE,
            self::PARAM_END_DATE,
            self::PARAM_EXPAND,
            self::PARAM_GROUP
        ], null);

        $params = array_merge($defaultParams, $params);

        $startDate = $this->dateUtil->getDateTime($params[self::PARAM_START_DATE], true);
        $endDate = $this->dateUtil->getDateTime($params[self::PARAM_END_DATE]);
        $expanded = filter_var($params[self::PARAM_EXPAND], FILTER_VALIDATE_BOOLEAN);
        $grouped = filter_var($params[self::PARAM_GROUP], FILTER_VALIDATE_BOOLEAN);

        $params = new Params($startDate, $endDate, $expanded, $grouped);

        if (is_array($reportType)) {
            return $this->reportSelector->getMultipleReports($reportType, $params);
        }

        return $this->reportSelector->getReports($reportType, $params);
    }
}