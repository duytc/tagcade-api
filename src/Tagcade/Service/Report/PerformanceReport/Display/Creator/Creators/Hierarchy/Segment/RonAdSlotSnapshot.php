<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Segment;

use Tagcade\Entity\Report\PerformanceReport\Display\Segment\RonAdSlotReport;
use Tagcade\Model\Core\SegmentInterface as SegmentModelInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\RonAdSlot as RonAdSlotReportType;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\BillableSnapshotCreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform\ConstructCalculatedReportTrait;

class RonAdSlotSnapshot extends BillableSnapshotCreatorAbstract implements RonAdSlotInterface
{
    use ConstructCalculatedReportTrait;
    /**
     * @var LibrarySlotTagRepositoryInterface
     */
    private $ronAdTagRepository;

    public function __construct(LibrarySlotTagRepositoryInterface $ronAdTagRepository, BillingCalculatorInterface $billingCalculator)
    {
        parent::__construct($billingCalculator);

        $this->ronAdTagRepository = $ronAdTagRepository;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(RonAdSlotReportType $reportType)
    {
        $ronAdSlot = $reportType->getRonAdSlot();
        $segment = $reportType->getSegment();

        $report = new RonAdSlotReport();
        $report
            ->setRonAdSlot($ronAdSlot)
            ->setSegment($segment)
            ->setDate($this->getDate())
        ;

        $ronAdSlotReportCounts[] = $this->eventCounter->getRonAdSlotReport($ronAdSlot->getId(), $segment instanceof SegmentModelInterface ? $segment->getId() : null);

        $ronAdTagIds = $this->ronAdTagRepository->getLibrarySlotTagIdsByLibraryAdSlot($ronAdSlot->getLibraryAdSlot());
        $ronAdTagCounts = $this->eventCounter->getRonAdTagReports($ronAdTagIds, $segment instanceof SegmentModelInterface ? $segment->getId() : null);

        $this->parseRawReportData($report, array_merge($ronAdSlotReportCounts, $ronAdTagCounts));

        return $report;
    }


    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof RonAdSlotReportType;
    }
}