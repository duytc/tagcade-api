<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Segment;

use Tagcade\Entity\Report\PerformanceReport\Display\Segment\SegmentReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\RonAdSlot as RonAdSlotReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\Segment as SegmentReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\HasSubReportsTrait;

class Segment extends CreatorAbstract implements SegmentInterface
{
    use HasSubReportsTrait;

    public function __construct(RonAdSlotInterface $subReportCreator)
    {
        $this->subReportCreator = $subReportCreator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(ReportTypeInterface $reportType)
    {
        $this->syncEventCounterForSubReports();

        $report = new SegmentReport();

        /** @var SegmentReportType $reportType */
        $segment = $reportType->getSegment();

        $report
            ->setSegment($segment)
            ->setDate($this->getDate());

        $ronAdSlots = $segment->getReportableRonAdSlots();
        if (count($ronAdSlots) > 0) {
            foreach ($ronAdSlots as $ronAdSlot) {
                $report->addSubReport(
                    $this->subReportCreator->createReport(new RonAdSlotReportType($ronAdSlot, $segment))
                        ->setSuperReport($report)
                );
            }
        }

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof SegmentReportType;
    }
}