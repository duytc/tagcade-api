<?php

namespace Tagcade\Service\Statistics\Provider;

use DateInterval;
use DatePeriod;
use DateTime;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform;
use Tagcade\Domain\DTO\Statistics\Summary\AccountSummary;
use Tagcade\Domain\DTO\Statistics\Summary\Summary;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork as AdNetworkReportTypes;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformReportTypes;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportRepositoryInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\ProjectedBillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;
use Tagcade\Service\Statistics\Provider\Behaviors\TopListFilterTrait;

class AccountStatistics implements AccountStatisticsInterface
{
    use TopListFilterTrait;

    /** @var ReportBuilderInterface */
    protected $reportBuilder;

    /** @var ProjectedBillingCalculatorInterface */
    protected $projectedBillingCalculator;

    /** @var PublisherManagerInterface */
    protected $userManager;

    /** @var AccountReportRepositoryInterface */
    protected $accountReportRepository;

    /** @var DateUtilInterface */
    protected $dateUtil;

    public function __construct(
        ReportBuilderInterface $reportBuilder,
        ProjectedBillingCalculatorInterface $projectedBillingCalculator,
        PublisherManagerInterface $userManager,
        AccountReportRepositoryInterface $accountReportRepository,
        DateUtilInterface $dateUtil
    )
    {
        $this->reportBuilder = $reportBuilder;
        $this->projectedBillingCalculator = $projectedBillingCalculator;
        $this->userManager = $userManager;
        $this->accountReportRepository = $accountReportRepository;
        $this->dateUtil = $dateUtil;
    }

    public function getTopPublishersByBilledAmount(Params $params, $limit = 10)
    {
        $topPublishers = $this->accountReportRepository->getTopPublishersByBilledAmount($params->getStartDate(), $params->getEndDate(), $limit);
        $myPublishers = [];
        foreach ($topPublishers as $publisherObj) {
            if (!array_key_exists('id', $publisherObj)) {
                throw new \LogicException('Expect id in publisher object');
            }

            $myPublishers[] = $publisherObj['id'];
        }

        $params->setGrouped(true);
        $allPublishersReports = $this->reportBuilder->getPublishersReport($myPublishers, $params);

        return $this->topList($allPublishersReports, $sortBy = 'billedAmount', $limit);
    }

    public function getTopAdNetworksByEstRevenueForPublisher(PublisherInterface $publisher, Params $params, $limit = 10)
    {
        $params->setGrouped(true);
        $adNetworksReports = $this->reportBuilder->getPublisherAdNetworksReport($publisher, $params);

        return $this->topList($adNetworksReports, $sortBy = 'estRevenue', $limit);
    }

    public function getProjectedBilledAmount(PublisherInterface $publisher)
    {
        return $this->projectedBillingCalculator->calculateProjectedBilledAmountForPublisher($publisher);
    }

    public function getAllPublishersProjectedBilledAmount()
    {
        $publishers = $this->userManager->allPublishers();

        $sumProjectedBilledAmount = 0;

        foreach ($publishers as $publisher) {
            $sumProjectedBilledAmount += $this->projectedBillingCalculator->calculateProjectedBilledAmountForPublisher($publisher);
        }

        return $sumProjectedBilledAmount;
    }

    public function getAccountSummaryByMonth(PublisherInterface $publisher, DateTime $startMonth, DateTime $endMonth = null)
    {
        $this->validateMonthRange($startMonth, $endMonth);

        $interval = new DateInterval('P1M');
        $monthRange = new DatePeriod($startMonth, $interval, $endMonth);

        $summaryByMonth = [];

        foreach ($monthRange as $month) {
            $summaryByMonth[] = $this->getAccountSummaryForMonth($publisher, $month);
        }

        return $summaryByMonth;
    }

    protected function getAccountSummaryForMonth(PublisherInterface $publisher, DateTime $month = null)
    {
        if (null === $month) {
            $month = new DateTime('today');
            $month = $month->modify('-1 month');
        }

        $this->validateMonth($month);

        $summary = $this->accountReportRepository->getStatsSummaryForPublisher($publisher, $this->dateUtil->getFirstDateInMonth($month), $this->dateUtil->getLastDateInMonth($month));

        return new AccountSummary(
            $publisher,
            $month,
            new Summary((int)$summary['slotOpportunities'], (int)$summary['totalOpportunities'], (int)$summary['impressions'], (float)$summary['totalBilledAmount'], (float)$summary['totalEstRevenue'])
        );
    }

    protected function validateMonth(DateTime $month)
    {
        $month = $this->dateUtil->getFirstDateInMonth($month);
        $thisMonth = $this->dateUtil->getFirstDateInMonth(new DateTime('today'));

        if ($month >= $thisMonth) {
            throw new InvalidArgumentException('Expect last month or further in the past');
        }
    }

    protected function validateMonthRange(DateTime $startMonth, DateTime $endMonth)
    {
        if (null === $endMonth) {
            $endMonth = new DateTime('today');
            $endMonth = $endMonth->modify('-1 month');
        }

        if ($startMonth > $endMonth) {
            throw new InvalidArgumentException('Start month must not exceed end month');
        }
    }
}