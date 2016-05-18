<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\Hierarchy\AdNetwork;

use DateTime;
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
        return $this->repository->getReportFor($reportType->getSite(), $reportType->getAdNetwork(), $startDate, $endDate);
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof SiteReportType;
    }
}