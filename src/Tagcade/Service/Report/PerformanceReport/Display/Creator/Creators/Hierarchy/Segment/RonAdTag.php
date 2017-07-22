<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Segment;

use Tagcade\Entity\Report\PerformanceReport\Display\Segment\RonAdTagReport;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\SegmentInterface as SegmentModelInterface;
use Tagcade\Model\Report\PerformanceReport\CalculateAdOpportunitiesTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\RonAdTag as RonAdTagReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\SourceReport\Report;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\EstCpmCalculatorInterface;

class RonAdTag extends CreatorAbstract implements RonAdTagInterface
{
    use CalculateAdOpportunitiesTrait;

    /**
     * @var EstCpmCalculatorInterface
     */
    private $estCpmCalculator;

    function __construct(EstCpmCalculatorInterface $revenueCalculator)
    {
        $this->estCpmCalculator = $revenueCalculator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(ReportTypeInterface $reportType)
    {
        $report = new RonAdTagReport();

        /** @var RonAdTagReportType $reportType */
        $ronAdTag = $reportType->getRonAdTag();
        $segment = $reportType->getSegment();
        $segmentId = $segment instanceof SegmentModelInterface ? $segment->getId(): null;
        $totalOpportunities = $this->eventCounter->getRonOpportunityCount($ronAdTag->getId(), $segmentId);
        $impressions = $this->eventCounter->getRonImpressionCount($ronAdTag->getId(), $segmentId);
        $isNativeAdSlot = $reportType->getRonAdTag()->getLibraryAdSlot() instanceof LibraryNativeAdSlotInterface;
        $firstOpportunities = $isNativeAdSlot ? $totalOpportunities : $this->eventCounter->getRonFirstOpportunityCount($ronAdTag->getId(), $segmentId);
        $verifiedImpressions = $isNativeAdSlot ? $impressions : $this->eventCounter->getRonVerifiedImpressionCount($ronAdTag->getId(), $segmentId);
        $report
            ->setRonAdTag($ronAdTag)
            ->setSegment($reportType->getSegment())
            ->setDate($this->getDate())
            ->setTotalOpportunities($totalOpportunities)
            ->setImpressions($impressions)
            ->setFirstOpportunities($firstOpportunities)
            ->setVerifiedImpressions($verifiedImpressions)
            ->setEstCpm($this->estCpmCalculator->getEstCpmForAdTag($ronAdTag, $this->getDate()))
            ->setAdOpportunities($this->calculateAdOpportunities($totalOpportunities));
        ;

        if (!$isNativeAdSlot) {
            $passbacks = $this->eventCounter->getPassbackCount($ronAdTag->getId());

            $report
                ->setPassbacks($this->eventCounter->getRonPassbackCount($ronAdTag->getId(), $segmentId))
                ->setUnverifiedImpressions($this->eventCounter->getRonUnverifiedImpressionCount($ronAdTag->getId(), $segmentId))
                ->setBlankImpressions($this->eventCounter->getRonBlankImpressionCount($ronAdTag->getId(), $segmentId))
                ->setVoidImpressions($this->eventCounter->getRonVoidImpressionCount($ronAdTag->getId(), $segmentId))
                ->setClicks($this->eventCounter->getRonClickCount($ronAdTag->getId(), $segmentId))
                ->setPosition($ronAdTag->getPosition())
                ->setAdOpportunities($this->calculateAdOpportunities($totalOpportunities, $passbacks));
            ;
        }

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof RonAdTagReportType;
    }
}