<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\AdNetwork;

use Tagcade\Entity\Report\PerformanceReport\Display\AdNetwork\AdTagReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Report\PerformanceReport\CalculateAdOpportunitiesTrait;
use Tagcade\Model\Report\PerformanceReport\CalculateNetworkOpportunityFillRateTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\AdTag as AdTagReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\EstCpmCalculatorInterface;

class AdTagSnapshot extends SnapshotCreatorAbstract implements AdTagInterface, SnapshotCreatorInterface
{
    use CalculateAdOpportunitiesTrait;
    use CalculateNetworkOpportunityFillRateTrait;

    /**
     * @var EstCpmCalculatorInterface
     */
    private $cpmCalculator;

    function __construct(EstCpmCalculatorInterface $revenueCalculator)
    {
        $this->cpmCalculator = $revenueCalculator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(ReportTypeInterface $reportType)
    {
        $report = new AdTagReport();

        /** @var AdTagReportType $reportType */
        $adTag = $reportType->getAdTag();
        $isNativeAdSlot = $reportType->getAdTag()->getAdSlot() instanceof NativeAdSlotInterface;

        $report
            ->setAdTag($adTag)
            ->setName($adTag->getName())
            ->setDate($this->getDate());

        $adTagReportCount = $this->eventCounter->getAdTagReport($adTag->getId(), $isNativeAdSlot);

        $this->parseRawReportData($report, array($adTagReportCount));

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdTagReportType;
    }

    /**
     * @inheritdoc
     */
    protected function constructReportModel(ReportInterface $report, array $data)
    {
        if (!$report instanceof AdTagReport) {
            throw new InvalidArgumentException('Expect WaterfallTagReport instance');
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
            ->setNetworkOpportunityFillRate($this->calculateNetworkOpportunityFillRate($report->getAdOpportunities(), $report->getTotalOpportunities()))
            ->setRefreshes($data[self::CACHE_KEY_REFRESHES]);

        // TODO latter
        $report->setEstCpm((float)0);
        $report->setEstRevenue((float)0);
    }
}