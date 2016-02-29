<?php

namespace Tagcade\Service\Report\RtbReport\Selector\Selectors\Hierarchy;

use DateTime;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\RtbReport\Hierarchy\SiteReportRepositoryInterface;
use Tagcade\Service\Report\RtbReport\Selector\Selectors\AbstractSelector;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\Site as SiteReportType;

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

    protected function doGetReports(SiteReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $this->repository->getReportFor($reportType->getSite(), $startDate, $endDate);
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof SiteReportType;
    }
}