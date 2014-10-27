<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorAbstract;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\AdTagReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\AdTag as AdTagReportType;

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
            ->setPosition($adTag->getPosition())
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