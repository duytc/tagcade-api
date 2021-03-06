<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\AdNetwork;

use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\AdNetwork\AdNetworkReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\CalculateAdOpportunitiesTrait;
use Tagcade\Model\Report\PerformanceReport\CalculateNetworkOpportunityFillRateTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\AdNetwork as AdNetworkReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\HasSubReportsTrait;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;

class AdNetworkSnapshot extends SnapshotCreatorAbstract implements AdNetworkInterface, SnapshotCreatorInterface
{
    use HasSubReportsTrait;
    use CalculateAdOpportunitiesTrait;
    use CalculateNetworkOpportunityFillRateTrait;

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
        $report = new AdNetworkReport();

        /** @var AdNetworkReportType $reportType */
        $adNetwork = $reportType->getAdNetwork();

        $report
            ->setAdNetwork($adNetwork)
            ->setName($adNetwork->getName())
            ->setDate($this->getDate());

        $adTagIdsForAdNetwork = $this->adTagManager->getAdTagIdsForAdNetwork($adNetwork);
        $networkCount = $this->eventCounter->getNetworkReport($adTagIdsForAdNetwork);

        $this->parseRawReportData($report, $networkCount);

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function parseRawReportData(ReportInterface $report, array $redisReportData)
    {
        $this->constructReportModel($report, $redisReportData);
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdNetworkReportType;
    }

    /**
     * @inheritdoc
     */
    protected function constructReportModel(ReportInterface $report, array $data)
    {
        if (!$report instanceof AdNetworkReport) {
            throw new InvalidArgumentException('Expect AdNetworkReport');
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
            ->setAdOpportunities($this->calculateAdOpportunities($report->getTotalOpportunities(), $report->getPassbacks()))
            ->setNetworkOpportunityFillRate($this->calculateNetworkOpportunityFillRate($report->getAdOpportunities(), $report->getTotalOpportunities()));

        // TODO latter
        $report->setEstCpm((float)0);
        $report->setEstRevenue((float)0);
    }
}