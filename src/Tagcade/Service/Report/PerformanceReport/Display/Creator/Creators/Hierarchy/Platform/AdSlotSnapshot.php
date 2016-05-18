<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\AdSlotReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\AdSlot as AdSlotReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\BillableSnapshotCreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;

class AdSlotSnapshot extends BillableSnapshotCreatorAbstract implements AdSlotInterface, SnapshotCreatorInterface
{
    use ConstructCalculatedReportTrait;

    /** @var AdTagManagerInterface */
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
            ->setDate($this->getDate());

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
}