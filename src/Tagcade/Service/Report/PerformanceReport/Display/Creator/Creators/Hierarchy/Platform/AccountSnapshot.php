<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\AccountReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Account as AccountReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\HasSubReportsTrait;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\BillableSnapshotCreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;

class AccountSnapshot extends BillableSnapshotCreatorAbstract implements AccountInterface, SnapshotCreatorInterface
{
    use HasSubReportsTrait;
    use ConstructCalculatedReportTrait;

    /** @var AdSlotManagerInterface */
    private $adSlotManager;

    /** @var AdTagManagerInterface */
    private $adTagManager;

    public function __construct(AdSlotManagerInterface $adSlotManager, AdTagManagerInterface $adTagManager, BillingCalculatorInterface $billingCalculator)
    {
        parent::__construct($billingCalculator);

        $this->adSlotManager = $adSlotManager;
        $this->adTagManager = $adTagManager;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(AccountReportType $reportType)
    {
        /* OLD METHOD: get accountReport snapshot via getting all ad slots, ad tags data
        $report = new AccountReport();
        $publisher = $reportType->getPublisher();
        $publisherName = $publisher->getCompany();
        $report
            ->setPublisher($publisher)
            ->setName($publisherName === null ? $publisher->getUser()->getUsername() : $publisherName)
            ->setDate($this->getDate());

        $reportableAdSlotIds = $this->adSlotManager->getReportableAdSlotIdsForPublisher($publisher);
        $adSlotReportCounts = $this->eventCounter->getAdSlotReports($reportableAdSlotIds);
        unset($reportableAdSlotIds);

        $adTagIdsForPublisher = $this->adTagManager->getActiveAdTagsIdsForPublisher($publisher);
        $adTagReportCounts = $this->eventCounter->getAdTagReports($adTagIdsForPublisher);
        unset($adTagIdsForPublisher);

        $this->parseRawReportData($report, array_merge($adSlotReportCounts, $adTagReportCounts));

        return $report; /**/

        /* NEW METHOD: get accountReport snapshot directly via redis */
        $report = new AccountReport();
        $publisher = $reportType->getPublisher();
        $publisherName = $publisher->getCompany();

        $report
            ->setPublisher($publisher)
            ->setName($publisherName === null ? $publisher->getUser()->getUsername() : $publisherName)
            ->setDate($this->getDate());

        $accountReportCount = $this->eventCounter->getAccountReport($publisher->getId());

        $result = array(
            self::CACHE_KEY_SLOT_OPPORTUNITY => $accountReportCount->getSlotOpportunities(),
            self::CACHE_KEY_OPPORTUNITY => $accountReportCount->getOpportunities(),
            self::CACHE_KEY_RTB_IMPRESSION => $accountReportCount->getRtbImpression(),
            self::CACHE_KEY_IMPRESSION => $accountReportCount->getImpression(),
            self::CACHE_KEY_PASSBACK => $accountReportCount->getPassbacks(),
            self::CACHE_KEY_HEADER_BID_REQUEST => $accountReportCount->getHbRequests(),
            self::CACHE_KEY_IN_BANNER_REQUEST => $accountReportCount->getInBannerRequests(),
            self::CACHE_KEY_IN_BANNER_TIMEOUT => $accountReportCount->getInBannerTimeouts(),
            self::CACHE_KEY_IN_BANNER_IMPRESSION => $accountReportCount->getInBannerImpressions(),
        );

        $this->parseRawReportData($report, $result);

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AccountReportType;
    }

    public function parseRawReportData(ReportInterface $report, array $redisReportData)
    {
        $this->constructReportModel($report, $redisReportData);
    }


    protected function constructReportModel(ReportInterface $report, array $data)
    {
        if (!$report instanceof AccountReport) {
            throw new InvalidArgumentException('Expect instance WaterfallTagReport');
        }

        // TODO set RTB, HB, ... for account report

        $report
            ->setTotalOpportunities($data[self::CACHE_KEY_OPPORTUNITY])
            ->setSlotOpportunities($data[self::CACHE_KEY_SLOT_OPPORTUNITY])
            ->setImpressions($data[self::CACHE_KEY_IMPRESSION])
            ->setRtbImpressions($data[self::CACHE_KEY_RTB_IMPRESSION])
            ->setPassbacks($data[self::CACHE_KEY_PASSBACK])
            ->setInBannerImpressions($data[self::CACHE_KEY_IN_BANNER_IMPRESSION])
            ->setInBannerTimeouts($data[self::CACHE_KEY_IN_BANNER_TIMEOUT])
            ->setInBannerRequests($data[self::CACHE_KEY_IN_BANNER_REQUEST])
            ->setFillRate()
        ;

        // TODO latter
        $report->setEstCpm((float)0);
        $report->setEstRevenue((float)0);

        $publisher = $report->getPublisher();

        $rateAmount = $this->billingCalculator->calculateBilledAmountForPublisher($report->getDate(), $publisher, $report->getSlotOpportunities());

        $report->setBilledAmount($rateAmount->getAmount());
        $report->setBilledRate($rateAmount->getRate()->getCpmRate());

        $inBannerRateAmount = $this->billingCalculator->calculateInBannerBilledAmountForPublisher($report->getDate(), $publisher, $report->getInBannerImpressions());

        $report->setInBannerBilledAmount($inBannerRateAmount->getAmount());
        $report->setInBannerBilledRate($inBannerRateAmount->getRate()->getCpmRate());
    }


}