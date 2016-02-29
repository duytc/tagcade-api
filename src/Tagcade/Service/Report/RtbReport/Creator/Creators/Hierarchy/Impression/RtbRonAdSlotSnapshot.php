<?php

namespace Tagcade\Service\Report\RtbReport\Creator\Creators\Hierarchy\Impression;


use Tagcade\Entity\Report\RtbReport\RonAdSlotReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Report\RtbReport\ReportInterface;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\RonAdSlot as RonAdSlotReportType;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\RtbReport\Creator\Creators\RtbSnapshotCreatorAbstract;

class RtbRonAdSlotSnapshot extends RtbSnapshotCreatorAbstract implements RtbRonAdSlotSnapshotInterface
{
    /**
     * @inheritdoc
     */
    public function doCreateReport(RonAdSlotReportType $reportType)
    {
        $report = new RonAdSlotReport();

        $ronAdSlot = $reportType->getRonAdSlot();

        $report
            ->setRonAdSlot($ronAdSlot)
            ->setSegment($reportType->getSegment())
            ->setName($ronAdSlot->getName())
            ->setDate($this->getDate());

        $ronAdSlotReportCounts[] = $this->eventCounter->getRtbRonAdSlotReport(
            $ronAdSlot->getId(),
            $reportType->getSegment() instanceof SegmentInterface ? $reportType->getSegment()->getId() : null
        );

        $this->parseRawReportData($report, $ronAdSlotReportCounts);

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

        $report
            ->setOpportunities($data[self::RESULT_KEY_SLOT_OPPORTUNITY])
            ->setImpressions($data[self::RESULT_KEY_IMPRESSION])
            ->setEarnedAmount($data[self::RESULT_KEY_PRICE])
            ->setFillRate();
    }
}