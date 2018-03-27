<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;


use Tagcade\Bundle\UserSystem\AdminBundle\Entity\User;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\BillingConfiguration;
use Tagcade\Model\Core\BillingConfigurationInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Site as SiteReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Repository\Core\BillingConfigurationRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\BillableSnapshotCreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;

class SiteSnapshot extends BillableSnapshotCreatorAbstract implements SiteInterface, SnapshotCreatorInterface
{
    use ConstructCalculatedReportTrait;

    /** @var AdSlotManagerInterface */
    private $adSlotManager;

    /** @var AdTagManagerInterface */
    private $adTagManager;

    public function __construct(AdSlotManagerInterface $adSlotManager, AdTagManagerInterface $adTagManager, BillingCalculatorInterface $billingCalculator, BillingConfigurationRepositoryInterface $billingConfigurationRepository)
    {
        parent::__construct($billingCalculator, $billingConfigurationRepository);

        $this->adSlotManager = $adSlotManager;
        $this->adTagManager = $adTagManager;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(ReportTypeInterface $reportType)
    {
        $siteReport = new SiteReport();

        /** @var SiteReportType $reportType */
        $site = $reportType->getSite();
        $siteReport
            ->setSite($site)
            ->setName($site->getName())
            ->setDate($this->getDate());

        $result = $this->eventCounter->getSiteReportData($site);

        $this->parseRawReportData($siteReport, $result);

        return $siteReport;
    }

    /**
     * @inheritdoc
     */
    public function parseRawReportData(ReportInterface $report, array $redisReportData)
    {
        if (!$report instanceof SiteReport) {
            throw new InvalidArgumentException('Expect instance WaterfallTagReport');
        }

        $this->constructReportModel($report, $redisReportData);

        $site = $report->getSite();

        $billingConfiguration = $this->billingConfigurationRepository->getConfigurationForModule($site->getPublisher(), User::MODULE_DISPLAY);
        if (!$billingConfiguration instanceof BillingConfigurationInterface) {
            $billingConfiguration = new BillingConfiguration();
            $billingConfiguration->setBillingFactor(BillingConfiguration::BILLING_FACTOR_SLOT_OPPORTUNITY);
        }
        
        $billingFactor = $billingConfiguration->getBillingFactor();
        if ($billingFactor == BillingConfiguration::BILLING_FACTOR_IMPRESSION_OPPORTUNITY) {
            $weight = $report->getAdOpportunities();
        } else {
            $weight = $report->getSlotOpportunities();
        }

        $rateAmount = $this->billingCalculator->calculateBilledAmountForPublisher($report->getDate(), $site->getPublisher(), $weight);

        $report->setBilledAmount($rateAmount->getAmount());
        $report->setBilledRate($rateAmount->getRate()->getCpmRate());

        $inBannerRateAmount = $this->billingCalculator->calculateInBannerBilledAmountForPublisher($report->getDate(), $site->getPublisher(), $report->getInBannerImpressions());

        $report->setInBannerBilledAmount($inBannerRateAmount->getAmount());
        $report->setInBannerBilledRate($inBannerRateAmount->getRate()->getCpmRate());
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return mixed
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof SiteReportType;
    }
}