<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\AdSlotReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\BillingConfiguration;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\AdSlot as AdSlotReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Repository\Core\BillingConfigurationRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\BillableSnapshotCreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;

class AdSlotSnapshot extends BillableSnapshotCreatorAbstract implements AdSlotInterface, SnapshotCreatorInterface
{
    use ConstructCalculatedReportTrait;

    /** @var AdTagManagerInterface */
    private $adTagManager;

    public function __construct(AdTagManagerInterface $adTagManager, BillingCalculatorInterface $billingCalculator, BillingConfigurationRepositoryInterface $billingConfigurationRepository)
    {
        parent::__construct($billingCalculator, $billingConfigurationRepository);

        $this->adTagManager = $adTagManager;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(ReportTypeInterface $reportType)
    {
        $adSlotReport = new AdSlotReport();

        /** @var AdSlotReportType $reportType */
        $adSlot = $reportType->getAdSlot();
        $adSlotReport
            ->setAdSlot($adSlot)
            ->setName($adSlot->getName())
            ->setDate($this->getDate());

        $result = $this->eventCounter->getAdSlotReport($adSlot);

        $this->parseRawReportData($adSlotReport, $result);

        return $adSlotReport;
    }

    /**
     * @inheritdoc
     */
    public function parseRawReportData(ReportInterface $report, array $redisReportData)
    {
        if (!$report instanceof AdSlotReport) {
            throw new InvalidArgumentException('Expect instance WaterfallTagReport');
        }

        $this->constructReportModel($report, $redisReportData);

        // for ad slot only
        $report->setRefreshedSlotOpportunities($redisReportData[SnapshotCreatorInterface::CACHE_KEY_SLOT_OPPORTUNITY_REFRESHES]);

        $slot = $report->getAdSlot();

        $billingConfiguration = $this->billingConfigurationRepository->getConfigurationForModule($report->getAdSlot()->getSite()->getPublisher(), User::MODULE_DISPLAY);

        $billingFactor = $billingConfiguration->getBillingFactor();
        if ($billingFactor == BillingConfiguration::BILLING_FACTOR_IMPRESSION_OPPORTUNITY) {
            $weight = $report->getAdOpportunities();
        } else {
            $weight = $report->getSlotOpportunities();
        }

        $rateAmount = $this->billingCalculator->calculateBilledAmountForPublisher($report->getDate(), $slot->getSite()->getPublisher(), $weight);

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