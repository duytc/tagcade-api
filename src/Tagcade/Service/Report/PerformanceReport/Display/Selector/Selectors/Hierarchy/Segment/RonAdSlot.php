<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\Hierarchy\Segment;

use DateTime;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\RonAdSlot as RonAdSlotReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Segment\RonAdSlotReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\AbstractSelector;

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

    /**
     * @inheritdoc
     */
    protected function doGetReports(ReportTypeInterface $reportType, DateTime $startDate, DateTime $endDate, $queryParams = null)
    {
        /** @var RonAdSlotReportType $reportType */
        if ($reportType->getSegment() instanceof SegmentInterface) {
            return $this->repository->getReportForRonSegment($reportType->getRonAdSlot(), $reportType->getSegment(), $startDate, $endDate);
        }

        return $this->repository->getReportForRonAdSlot($reportType->getRonAdSlot(), $startDate, $endDate);
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof RonAdSlotReportType;
    }
}