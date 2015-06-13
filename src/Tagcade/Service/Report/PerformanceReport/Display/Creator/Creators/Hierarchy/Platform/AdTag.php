<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\User\Role\Publisher;
use Tagcade\Service\Report\PerformanceReport\Display\EstCpmCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorAbstract;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\AdTagReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\AdTag as AdTagReportType;

class AdTag extends CreatorAbstract implements AdTagInterface
{
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
    public function doCreateReport(AdTagReportType $reportType)
    {
        $report = new AdTagReport();

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
            ->setEstCpm($this->estCpmCalculator->getEstCpmForAdTag($adTag, $this->getDate()))
        ;

        if (!$isNativeAdSlot) {
            $report
                ->setPassbacks($this->eventCounter->getPassbackCount($adTag->getId()))
                ->setUnverifiedImpressions($this->eventCounter->getUnverifiedImpressionCount($adTag->getId()))
                ->setBlankImpressions($this->eventCounter->getBlankImpressionCount($adTag->getId()))
                ->setVoidImpressions($this->eventCounter->getVoidImpressionCount($adTag->getId()))
                ->setClicks($this->eventCounter->getClickCount($adTag->getId()))
                ->setPosition($adTag->getPosition())
            ;
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