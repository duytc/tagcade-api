<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Segment;

use Tagcade\Entity\Report\PerformanceReport\Display\Segment\RonAdTagReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\SegmentInterface as SegmentModelInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\RonAdTag as RonAdTagReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorAbstract;

class RonAdTagSnapshot extends SnapshotCreatorAbstract implements RonAdTagInterface
{
    /**
     * @inheritdoc
     */
    public function doCreateReport(RonAdTagReportType $reportType)
    {
        $report = new RonAdTagReport();

        $ronAdTag = $reportType->getRonAdTag();
        $segment = $reportType->getSegment();
        $segmentId = $segment instanceof SegmentModelInterface ? $segment->getId(): null;
        $isNativeAdSlot = $reportType->getRonAdTag()->getLibraryAdSlot() instanceof LibraryNativeAdSlotInterface;

        $report
            ->setRonAdTag($ronAdTag)
            ->setSegment($reportType->getSegment())
            ->setDate($this->getDate())
        ;

        $ronTagReportCounts = $this->eventCounter->getRonAdTagReport($ronAdTag->getId(), $segmentId, $isNativeAdSlot);

        $this->parseRawReportData($report, array($ronTagReportCounts));

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof RonAdTagReportType;
    }

    protected function constructReportModel(ReportInterface $report, array $data)
    {
        if (!$report instanceof RonAdTagReport) {
            throw new InvalidArgumentException('Expect instance of RonAdTagReport');
        }

        $isNativeAdSlot = $report->getRonAdTag()->getLibraryAdSlot() instanceof LibraryNativeAdSlotInterface;
        $totalOpportunities = $data[self::CACHE_KEY_OPPORTUNITY];
        $impressions = $data[self::CACHE_KEY_IMPRESSION];
        $firstOpportunities = $isNativeAdSlot ? $totalOpportunities : $data[self::CACHE_KEY_FIRST_OPPORTUNITY];
        $verifiedImpressions = $isNativeAdSlot ? $impressions : $data[self::CACHE_KEY_VERIFIED_IMPRESSION];

        $report
            ->setTotalOpportunities($totalOpportunities)
            ->setImpressions($impressions)
            ->setFirstOpportunities($firstOpportunities)
            ->setVerifiedImpressions($verifiedImpressions)
            ->setFillRate()
        ;

        if (!$isNativeAdSlot) {
            $report
                ->setPassbacks($data[self::CACHE_KEY_PASSBACK])
                ->setUnverifiedImpressions($data[self::CACHE_KEY_UNVERIFIED_IMPRESSION])
                ->setBlankImpressions($data[self::CACHE_KEY_BLANK_IMPRESSION])
                ->setVoidImpressions($data[self::CACHE_KEY_VOID_IMPRESSION])
                ->setClicks($data[self::CACHE_KEY_CLICK])
                ->setPosition($report->getRonAdTag()->getPosition())
            ;
        }
    }
}