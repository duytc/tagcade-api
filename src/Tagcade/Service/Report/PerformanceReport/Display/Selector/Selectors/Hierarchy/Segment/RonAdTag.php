<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\Hierarchy\Segment;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\RonAdTag as RonAdTagReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Segment\RonAdTagReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\AbstractSelector;

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

    /**
     * @inheritdoc
     */
    protected function doGetReports(ReportTypeInterface $reportType, DateTime $startDate, DateTime $endDate, $queryParams = null)
    {
        /** @var RonAdTagReportType $reportType */
        return $this->repository->getReportFor($reportType->getRonAdTag(), $startDate, $endDate);
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof RonAdTagReportType;
    }
}