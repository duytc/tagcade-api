<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\Hierarchy\Segment;

use DateTime;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Segment\RonAdTagReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\AbstractSelector;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\RonAdTag as RonAdTagReportType;

class RonAdTag extends AbstractSelector
{
    /**
     * @var RonAdTagReportRepositoryInterface
     */
    protected $repository;

    public function __construct(RonAdTagReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function doGetReports(RonAdTagReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        return $this->repository->getReportFor($reportType->getRonAdTag(), $startDate, $endDate);
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof RonAdTagReportType;
    }
}