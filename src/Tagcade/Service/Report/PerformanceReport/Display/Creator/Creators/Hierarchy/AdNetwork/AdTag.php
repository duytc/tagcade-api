<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\AdNetwork;

use Tagcade\Service\Report\PerformanceReport\Display\EstCpmCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorAbstract;
use Tagcade\Entity\Report\PerformanceReport\Display\AdNetwork\AdTagReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\AdTag as AdTagReportType;

class AdTag extends CreatorAbstract implements AdTagInterface
{

    /**
     * @var EstCpmCalculatorInterface
     */
    private $cpmCalculator;

    function __construct(EstCpmCalculatorInterface $revenueCalculator)
    {
        $this->cpmCalculator = $revenueCalculator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(AdTagReportType $reportType)
    {
        $report = new AdTagReport();

        $adTag = $reportType->getAdTag();
        $totalOpportunities = $this->eventCounter->getOpportunityCount($adTag->getId());

        $report
            ->setAdTag($adTag)
            ->setDate($this->getDate())
            ->setTotalOpportunities($totalOpportunities)
            ->setImpressions($this->eventCounter->getImpressionCount($adTag->getId()))
            ->setFirstOpportunities($this->eventCounter->getFirstOpportunityCount($adTag->getId()))
            ->setVerifiedImpressions($this->eventCounter->getVerifiedImpressionCount($adTag->getId()))
            ->setUnverifiedImpressions($this->eventCounter->getUnverifiedImpressionCount($adTag->getId()))
            ->setBlankImpressions($this->eventCounter->getBlankImpressionCount($adTag->getId()))
            ->setPassbacks($this->eventCounter->getPassbackCount($adTag->getId()))
            ->setEstCpm($this->cpmCalculator->getEstCpmForAdTag($adTag, $this->getDate()))
        ;

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