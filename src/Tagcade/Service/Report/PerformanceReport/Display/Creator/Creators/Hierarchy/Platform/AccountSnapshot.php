<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\Bundle\UserSystem\AdminBundle\Entity\User;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\AccountReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\BillingConfiguration;
use Tagcade\Model\Core\BillingConfigurationInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Account as AccountReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Site as SiteReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Repository\Core\BillingConfigurationRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\HasSubReportsTrait;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\BillableSnapshotCreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;

class AccountSnapshot extends BillableSnapshotCreatorAbstract implements AccountInterface, SnapshotCreatorInterface
{
    use HasSubReportsTrait;
    use ConstructCalculatedReportTrait;

    /** @var SiteManagerInterface */
    private $siteManager;

    /** @var AdSlotManagerInterface */
    private $adSlotManager;

    /** @var AdTagManagerInterface */
    private $adTagManager;

    /** @var SiteSnapshot */
    private $siteSnapshotCreator;

    public function __construct(SiteManagerInterface $siteManager, AdSlotManagerInterface $adSlotManager, AdTagManagerInterface $adTagManager, BillingCalculatorInterface $billingCalculator, BillingConfigurationRepositoryInterface $billingConfigurationRepository, SiteSnapshot $siteSnapshotCreator)
    {
        parent::__construct($billingCalculator, $billingConfigurationRepository);

        $this->siteManager = $siteManager;
        $this->adSlotManager = $adSlotManager;
        $this->adTagManager = $adTagManager;
        $this->siteSnapshotCreator = $siteSnapshotCreator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(ReportTypeInterface $reportType)
    {
        $accountReport = new AccountReport();

        /** @var AccountReportType $reportType */
        $publisher = $reportType->getPublisher();
        $publisherName = $publisher->getCompany();

        $accountReport
            ->setPublisher($publisher)
            ->setName($publisherName === null ? $publisher->getUser()->getUsername() : $publisherName)
            ->setDate($this->getDate());

        $result = $this->eventCounter->getAccountReport($publisher);

        $this->parseRawReportData($accountReport, $result);

        // aggregate ad Tag Snapshot reports
        $estRevenue = 0;
        $sites = $this->siteManager->getSitesForPublisher($publisher);
        $this->siteSnapshotCreator->setEventCounter($this->eventCounter);
        foreach ($sites as $site) {
            $siteSnapshotReport = $this->siteSnapshotCreator->createReport(new SiteReportType($site));
            $estRevenue += $siteSnapshotReport->getEstRevenue();
        }

        $accountReport->setEstRevenue($estRevenue);

        return $accountReport;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AccountReportType;
    }

    /**
     * @inheritdoc
     */
    public function parseRawReportData(ReportInterface $report, array $redisReportData)
    {
        if (!$report instanceof AccountReport) {
            throw new InvalidArgumentException('Expect instance WaterfallTagReport');
        }

        $this->constructReportModel($report, $redisReportData);

        $publisher = $report->getPublisher();

        $billingConfiguration = $this->billingConfigurationRepository->getConfigurationForModule($publisher, User::MODULE_DISPLAY);
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

        $rateAmount = $this->billingCalculator->calculateBilledAmountForPublisher($report->getDate(), $publisher, $weight);

        $report->setBilledAmount($rateAmount->getAmount());
        $report->setBilledRate($rateAmount->getRate()->getCpmRate());

        $inBannerRateAmount = $this->billingCalculator->calculateInBannerBilledAmountForPublisher($report->getDate(), $publisher, $report->getInBannerImpressions());

        $report->setInBannerBilledAmount($inBannerRateAmount->getAmount());
        $report->setInBannerBilledRate($inBannerRateAmount->getRate()->getCpmRate());
    }
}