<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\AdSlotReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\AdSlot as AdSlotReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\BillableSnapshotCreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;

class AdSlotSnapshot extends BillableSnapshotCreatorAbstract implements AdSlotInterface, SnapshotCreatorInterface
{
    /**
     * @var AdTagManagerInterface
     */
    private $adTagManager;

    public function __construct(AdTagManagerInterface $adTagManager, BillingCalculatorInterface $billingCalculator)
    {
        parent::__construct($billingCalculator);

        $this->adTagManager = $adTagManager;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(AdSlotReportType $reportType)
    {
        $report = new AdSlotReport();
        $adSlot = $reportType->getAdSlot();
        $report
            ->setAdSlot($adSlot)
            ->setName($adSlot->getName())
            ->setDate($this->getDate())
        ;

        $adSlotReportCounts = $this->eventCounter->getAdSlotReports(array($adSlot->getId()));

        $adTagIdsForAdSlot = $this->adTagManager->getAdTagIdsForAdSlot($adSlot);
        $adTagReportCounts = $this->eventCounter->getAdTagReports($adTagIdsForAdSlot);

        $this->parseRawReportData($report, array_merge($adTagReportCounts, $adSlotReportCounts));

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdSlotReportType;
    }

    protected function constructReportModel(ReportInterface $report, array $data)
    {
        if (!$report instanceof AdSlotReport) {
            throw new InvalidArgumentException('Expect instance AdTagReport');
        }

        $report->setTotalOpportunities($data[self::CACHE_KEY_OPPORTUNITY])
            ->setSlotOpportunities($data[self::CACHE_KEY_SLOT_OPPORTUNITY])
            ->setImpressions($data[self::CACHE_KEY_IMPRESSION])
            ->setPassbacks($data[self::CACHE_KEY_PASSBACK])
            ->setFillRate()
        ;

        // TODO latter
        $report->setEstCpm((float)0);
        $report->setEstRevenue((float)0);
    }
}