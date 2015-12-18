<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Segment;

use Tagcade\Entity\Report\PerformanceReport\Display\Segment\RonAdSlotReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\SegmentInterface as SegmentModelInterface;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorAbstract;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\HasSubReportsTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\RonAdSlot as RonAdSlotReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\RonAdTag as RonAdTagReportType;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\BillableSnapshotCreatorAbstract;

class RonAdSlotSnapshot extends BillableSnapshotCreatorAbstract implements RonAdSlotInterface
{
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

        $ronAdSlotReportCounts[] = $this->eventCounter->getRonAdSlotReport($ronAdSlot->getId(), $segment instanceof SegmentInterface ? $segment->getId() : null);

        $ronAdTagIds = $this->ronAdTagRepository->getLibrarySlotTagIdsByLibraryAdSlot($ronAdSlot->getLibraryAdSlot());
        $ronAdTagCounts = $this->eventCounter->getRonAdTagReports($ronAdTagIds, $segment instanceof SegmentInterface ? $segment->getId() : null);

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

    protected function constructReportModel(ReportInterface $report, array $data)
    {
        if (!$report instanceof RonAdSlotReport) {
            throw new InvalidArgumentException('Expect instance RonAdSlotReport');
        }

        $report->setTotalOpportunities($data[self::CACHE_KEY_OPPORTUNITY])
            ->setSlotOpportunities($data[self::CACHE_KEY_SLOT_OPPORTUNITY])
            ->setImpressions($data[self::CACHE_KEY_IMPRESSION])
            ->setPassbacks($data[self::CACHE_KEY_PASSBACK])
            ->setFillRate()
        ;
    }
}