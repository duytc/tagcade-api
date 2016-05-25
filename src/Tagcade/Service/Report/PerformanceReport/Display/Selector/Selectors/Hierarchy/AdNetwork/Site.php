<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\Hierarchy\AdNetwork;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\SiteReportInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\AbstractSelector;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork\SiteReportRepositoryInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\Site as SiteReportType;

class Site extends AbstractSelector
{
    /**
     * @var SiteReportRepositoryInterface
     */
    protected $repository;

    public function __construct(SiteReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function doGetReports(SiteReportType $reportType, DateTime $startDate, DateTime $endDate, $queryParams = null)
    {
        $reports = $this->repository->getReportFor($reportType->getSite(), $reportType->getAdNetwork(), $startDate, $endDate);

        // modify report name using ad network name if do get site reports breakdown by ad network also by day!!!
        if (is_array($reports) && $reportType->isGroupByAdNetwork()) {
            foreach ($reports as $report) {
                if ($report instanceof SiteReportInterface) {
                    $report->setName($reportType->getAdNetworkName());
                }
            }
        }

        return $reports;
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof SiteReportType;
    }
}