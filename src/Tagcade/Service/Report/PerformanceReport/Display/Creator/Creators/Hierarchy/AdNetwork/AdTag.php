<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\AdNetwork;

use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorAbstract;
use Tagcade\Entity\Report\PerformanceReport\Display\AdNetwork\AdTagReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\AdTag as AdTagReportType;

class AdTag extends CreatorAbstract implements AdTagInterface
{
    /**
     * @inheritdoc
     */
    public function doCreateReport(AdTagReportType $reportType)
    {
        $report = new AdTagReport();

        $adTag = $reportType->getAdTag();

        $report
            ->setAdTag($adTag)
            ->setDate($this->getDate())
            ->setTotalOpportunities($this->eventCounter->getOpportunityCount($adTag->getId()))
            ->setImpressions($this->eventCounter->getImpressionCount($adTag->getId()))
            ->setPassbacks($this->eventCounter->getPassbackCount($adTag->getId()))
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