<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\Hierarchy\Segment;

use DateTime;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\Segment;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Segment\RonAdSlotReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\AbstractSelector;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\RonAdSlot as RonAdSlotReportType;

class RonAdSlot extends AbstractSelector
{
    /**
     * @var RonAdSlotReportRepositoryInterface
     */
    protected $repository;

    public function __construct(RonAdSlotReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function doGetReports(RonAdSlotReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        if ($reportType->getSegment() instanceof SegmentInterface) {
            return $this->repository->getReportForRonSegment($reportType->getRonAdSlot(), $reportType->getSegment(), $startDate, $endDate);
        }
        else return $this->repository->getReportForRonAdSlot($reportType->getRonAdSlot(), $startDate, $endDate);
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof RonAdSlotReportType;
    }
}