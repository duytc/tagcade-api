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

        $result = $this->eventCounter->getAdSlotReport($adSlot);

        $this->parseRawReportData($report, $result);

        return $report;
    }

    public function parseRawReportData(ReportInterface $report, array $redisReportData)
    {
        if (!$report instanceof AdSlotReport) {
            throw new InvalidArgumentException('Expect instance WaterfallTagReport');
        }

        $this->constructReportModel($report, $redisReportData);

        $slot = $report->getAdSlot();

        $rateAmount = $this->billingCalculator->calculateBilledAmountForPublisher($report->getDate(), $slot->getSite()->getPublisher(), $report->getSlotOpportunities());

        $report->setBilledAmount($rateAmount->getAmount());
        $report->setBilledRate($rateAmount->getRate()->getCpmRate());

        $inBannerRateAmount = $this->billingCalculator->calculateInBannerBilledAmountForPublisher($report->getDate(), $slot->getSite()->getPublisher(), $report->getInBannerImpressions());

        $report->setInBannerBilledAmount($inBannerRateAmount->getAmount());
        $report->setInBannerBilledRate($inBannerRateAmount->getRate()->getCpmRate());
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdSlotReportType;
    }
}