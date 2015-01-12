<?php

namespace Tagcade\Service\Statistics\Provider;

use DateTime;
use DateInterval;
use DatePeriod;
use Tagcade\Bundle\UserBundle\DomainManager\UserManagerInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform;
use Tagcade\Domain\DTO\Statistics\MonthBilledAmount;
use Tagcade\Domain\DTO\Statistics\MonthRevenue;
use Tagcade\Domain\DTO\Statistics\PublisherBilledAmount;
use Tagcade\Domain\DTO\Statistics\PublisherRevenue;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportRepositoryInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\ProjectedBillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;
use Tagcade\Service\Statistics\Provider\Behaviors\TopListFilterTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformReportTypes;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork as AdNetworkReportTypes;

class AccountStatistics implements AccountStatisticsInterface
{
    use TopListFilterTrait;

    /**
     * @var ReportBuilderInterface
     */
    protected $reportBuilder;
    /**
     * @var ProjectedBillingCalculatorInterface
     */
    protected $projectedBillingCalculator;
    /**
     * @var PublisherManagerInterface
     */
    protected $userManager;
    /**
     * @var AccountReportRepositoryInterface
     */
    protected $accountReportRepository;
    /**
     * @var DateUtilInterface
     */
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
        $params->setGrouped(true);
        $allPublishersReports = $this->reportBuilder->getAllPublishersReport($params);

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

        foreach($publishers as $publisher) {
            $sumProjectedBilledAmount += $this->projectedBillingCalculator->calculateProjectedBilledAmountForPublisher($publisher);
        }

        return $sumProjectedBilledAmount;
    }

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startMonth
     * @param DateTime $endMonth
     * @return array
     */
    public function getAccountBilledAmountByMonth(
        PublisherInterface $publisher,
        DateTime $startMonth,
        DateTime $endMonth = null
    ) {

        $this->validateMonthRange($startMonth, $endMonth);

        $interval = new DateInterval('P1M');
        $monthRange = new DatePeriod($startMonth, $interval ,$endMonth);

        $billedAmounts = [];

        foreach($monthRange as $month) {
            $billedAmounts[] = $this->getAccountBilledAmountForMonth($publisher, $month);
        }

        return $billedAmounts;
    }

    public function getAccountRevenueByMonth(
        PublisherInterface $publisher,
        DateTime $startMonth,
        DateTime $endMonth = null
    ) {

        $this->validateMonthRange($startMonth, $endMonth);

        $interval = new DateInterval('P1M');
        $monthRange = new DatePeriod($startMonth, $interval ,$endMonth);

        $revenueByMonth = [];

        foreach($monthRange as $month) {
            $revenueByMonth[] = $this->getAccountRevenueForMonth($publisher, $month);
        }

        return $revenueByMonth;
    }

    protected function getAccountRevenueForMonth(PublisherInterface $publisher, DateTime $month = null)
    {
        if (null === $month) {
            $month = new DateTime('today');
            $month = $month->modify('-1 month');
        }

        $this->validateMonth($month);

        $revenue = $this->accountReportRepository->getSumRevenueForPublisher($publisher, $this->dateUtil->getFirstDateInMonth($month), $this->dateUtil->getLastDateInMonth($month));

        return new PublisherRevenue($publisher, new MonthRevenue($month, $revenue));
    }

    protected function getAccountBilledAmountForMonth(PublisherInterface $publisher, DateTime $month = null)
    {
        if (null === $month) {
            $month = new DateTime('today');
            $month = $month->modify('-1 month');
        }

        $this->validateMonth($month);

        $billedAmount = $this->accountReportRepository->getSumBilledAmountForPublisher($publisher, $this->dateUtil->getFirstDateInMonth($month), $this->dateUtil->getLastDateInMonth($month));

        return new PublisherBilledAmount($publisher, new MonthBilledAmount($month, $billedAmount));
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