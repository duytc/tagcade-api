<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\AdNetwork;

use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\AdNetwork\SiteReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\CalculateAdOpportunitiesTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\Site as AdNetworkSiteReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\HasSubReportsTrait;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;

class SiteSnapshot extends SnapshotCreatorAbstract implements SiteInterface, SnapshotCreatorInterface
{
    use HasSubReportsTrait;
    use CalculateAdOpportunitiesTrait;

    /** @var AdSlotManagerInterface */
    private $adSlotManager;

    /** @var AdTagManagerInterface */
    private $adTagManager;

    public function __construct(AdSlotManagerInterface $adSlotManager, AdTagManagerInterface $adTagManager)
    {
        $this->adSlotManager = $adSlotManager;
        $this->adTagManager = $adTagManager;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(ReportTypeInterface $reportType)
    {
        $report = new SiteReport();

        /** @var AdNetworkSiteReportType $reportType */
        $adNetwork = $reportType->getAdNetwork();
        $site = $reportType->getSite();
        $report
            ->setSite($site)
            ->setName($site->getName())
            ->setDate($this->getDate());

        if ($reportType->isGroupByAdNetwork()) {
            $report->setName($adNetwork->getName());
        }

        $adTagIds = $this->adTagManager->getAdTagIdsForAdNetworkAndSite($adNetwork, $site);
        $networkCount = $this->eventCounter->getNetworkReport($adTagIds);

        $this->parseRawReportData($report, $networkCount);

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdNetworkSiteReportType;
    }

    public function parseRawReportData(ReportInterface $report, array $redisReportData)
    {
        $this->constructReportModel($report, $redisReportData);
    }

    /**
     * @inheritdoc
     */
    protected function constructReportModel(ReportInterface $report, array $data)
    {
        if (!$report instanceof SiteReport) {
            throw new InvalidArgumentException('Expect SiteReport');
        }

        $report->setTotalOpportunities($data[self::CACHE_KEY_OPPORTUNITY])
            ->setImpressions($data[self::CACHE_KEY_IMPRESSION])
            ->setPassbacks($data[self::CACHE_KEY_PASSBACK])
            ->setFirstOpportunities($data[self::CACHE_KEY_FIRST_OPPORTUNITY])
            ->setVerifiedImpressions($data[self::CACHE_KEY_VERIFIED_IMPRESSION])
            ->setUnverifiedImpressions($data[self::CACHE_KEY_UNVERIFIED_IMPRESSION])
            ->setBlankImpressions($data[self::CACHE_KEY_BLANK_IMPRESSION])
            ->setVoidImpressions($data[self::CACHE_KEY_VOID_IMPRESSION])
            ->setClicks($data[self::CACHE_KEY_CLICK])
            ->setFillRate()
            ->setAdOpportunities($this->calculateAdOpportunities($report->getTotalOpportunities(), $report->getPassbacks()));

        // TODO latter
        $report->setEstCpm((float)0);
        $report->setEstRevenue((float)0);
    }
}