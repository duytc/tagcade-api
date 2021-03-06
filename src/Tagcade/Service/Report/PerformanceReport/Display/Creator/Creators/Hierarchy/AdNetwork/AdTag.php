<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\AdNetwork;

use Tagcade\Entity\Report\PerformanceReport\Display\AdNetwork\AdTagReport;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Report\PerformanceReport\CalculateAdOpportunitiesTrait;
use Tagcade\Model\Report\PerformanceReport\CalculateNetworkOpportunityFillRateTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\AdTag as AdTagReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\EstCpmCalculatorInterface;

class AdTag extends CreatorAbstract implements AdTagInterface
{
    use CalculateAdOpportunitiesTrait;
    use CalculateNetworkOpportunityFillRateTrait;

    /** @var EstCpmCalculatorInterface */
    private $cpmCalculator;

    function __construct(EstCpmCalculatorInterface $revenueCalculator)
    {
        $this->cpmCalculator = $revenueCalculator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(ReportTypeInterface $reportType)
    {
        $report = new AdTagReport();

        /** @var AdTagReportType $reportType */
        $adTag = $reportType->getAdTag();
        $totalOpportunities = $this->eventCounter->getOpportunityCount($adTag->getId());
        $impressions = $this->eventCounter->getImpressionCount($adTag->getId());

        $isNativeAdSlot = $reportType->getAdTag()->getAdSlot() instanceof NativeAdSlotInterface;
        $firstOpportunities = $isNativeAdSlot ? $totalOpportunities : $this->eventCounter->getFirstOpportunityCount($adTag->getId());
        $verifiedImpressions = $isNativeAdSlot ? $impressions : $this->eventCounter->getVerifiedImpressionCount($adTag->getId());

        $report
            ->setAdTag($adTag)
            ->setDate($this->getDate())
            ->setTotalOpportunities($totalOpportunities)
            ->setImpressions($impressions)
            ->setFirstOpportunities($firstOpportunities)
            ->setVerifiedImpressions($verifiedImpressions)
            ->setEstCpm($this->cpmCalculator->getEstCpmForAdTag($adTag, $this->getDate()))
            ->setAdOpportunities($this->calculateAdOpportunities($totalOpportunities))
            ->setNetworkOpportunityFillRate($this->calculateNetworkOpportunityFillRate($report->getAdOpportunities(), $totalOpportunities))
            ->setInBannerImpressions($this->eventCounter->getAdTagInBannerImpressionCount($adTag->getAdSlotId(), $adTag->getId()))
            ->setInBannerRequests($this->eventCounter->getAdTagInBannerRequestCount($adTag->getAdSlotId(), $adTag->getId()))
            ->setInBannerTimeouts($this->eventCounter->getAdTagInBannerTimeoutCount($adTag->getAdSlotId(), $adTag->getId()))
        ;

        if (!$isNativeAdSlot) {
            $passbacks = $this->eventCounter->getPassbackCount($adTag->getId());

            $report
                ->setPassbacks($passbacks)
                ->setUnverifiedImpressions($this->eventCounter->getUnverifiedImpressionCount($adTag->getId()))
                ->setBlankImpressions($this->eventCounter->getBlankImpressionCount($adTag->getId()))
                ->setVoidImpressions($this->eventCounter->getVoidImpressionCount($adTag->getId()))
                ->setClicks($this->eventCounter->getClickCount($adTag->getId()))
                ->setAdOpportunities($this->calculateAdOpportunities($totalOpportunities, $passbacks))
                ->setNetworkOpportunityFillRate($this->calculateNetworkOpportunityFillRate($report->getAdOpportunities(), $totalOpportunities))
                ->setRefreshes($this->eventCounter->getRefreshesCount($adTag->getId()));
        } else {
            $report
                ->setPassbacks(0)
                ->setRefreshes(0);
        }

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdTagReportType;
    }
}