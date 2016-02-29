<?php

namespace Tagcade\Service\Report\RtbReport\Selector\Selectors\Hierarchy;

use DateTime;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\RtbReport\Hierarchy\AdSlotReportRepositoryInterface;
use Tagcade\Repository\Report\RtbReport\Hierarchy\RonAdSlotReportRepositoryInterface;
use Tagcade\Service\Report\RtbReport\Selector\Selectors\AbstractSelector;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\RonAdSlot as RonAdSlotReportType;

class RonAdSlot extends AbstractSelector
{
    /**
     * @var AdSlotReportRepositoryInterface
     */
    protected $repository;

    public function __construct(RonAdSlotReportRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    protected function doGetReports(RonAdSlotReportType $reportType, DateTime $startDate, DateTime $endDate)
    {
        $segment = $reportType->getSegment();
        if ($segment instanceof SegmentInterface) {
            return $this->repository->getReportForRonSegment($reportType->getRonAdSlot(), $segment, $startDate, $endDate);
        }

        return $this->repository->getReportForRonAdSlot($reportType->getRonAdSlot(), $startDate, $endDate);
    }

    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof RonAdSlotReportType;
    }
}