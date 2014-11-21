<?php

namespace Tagcade\Service\Report\Statistics\Selector;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\PlatformReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Platform as PlatformType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface as StatisticsTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportSelectorInterface;
use Tagcade\Domain\DTO\Report\Statistics\PlatformStatisticsDTO;
use DateTime;

class PlatformStatistics extends AbstractSelector implements PlatformStatisticsInterface
{
    /**
     * @var ReportSelectorInterface
     */
    protected $reportSelector;

    /**
     * @var AccountStatisticsInterface
     */
    protected $accountStatistics;

    public function __construct(ReportSelectorInterface $reportSelector, AccountStatisticsInterface $accountStatistics)
    {
        $this->reportSelector = $reportSelector;
        $this->accountStatistics = $accountStatistics;
    }

    /**
     * @inheritdoc
     */
    public function doGetReports(PlatformType $platformType, DateTime $startDate = null, DateTime $endDate = null, $deepLength = 10)
    {
        $platformReports = $this->reportSelector->getReports($platformType, $startDate, $endDate, false);

        $statisticsForAccounts = $this->accountStatistics->getStatisticsForAllAccounts($startDate, $endDate);

        $totalOpportunities = 0;
        $totalBillingCost = 0;

        /**
         * @var PlatformReportInterface $report
         */
        foreach($platformReports as $report) {
            $totalOpportunities += $report->getTotalOpportunities();
            $totalBillingCost += $report->getBillingCost();
        }

        $topAccounts = [];

        if(!is_null($statisticsForAccounts))
        {
            $topAccounts = $this->topList($statisticsForAccounts, $deepLength);
        }

        return new PlatformStatisticsDTO($startDate, $endDate, $totalBillingCost, $totalOpportunities, $topAccounts);
    }

    /**
     * @param StatisticsTypeInterface $statisticsType
     * @return bool
     */
    public function supportsStatisticsType(StatisticsTypeInterface $statisticsType)
    {
        return $statisticsType instanceof PlatformType;
    }


}
