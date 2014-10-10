<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType;

use Tagcade\Model\Core\AdTagInterface as AdTagModelInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\AdTagReport;

class AdTag extends ReportTypeAbstract implements AdTagInterface
{
    /**
     * @inheritdoc
     */
    public function doCreateReport(AdTagModelInterface $adTag)
    {
        $report = new AdTagReport();

        $report
            ->setAdTag($adTag)
            ->setDate($this->getDate())
            ->setName($adTag->getName())
            ->setOpportunities($this->eventCounter->getOpportunityCount($adTag->getId()))
            ->setImpressions($this->eventCounter->getImpressionCount($adTag->getId()))
            ->setPassbacks($this->eventCounter->getPassbackCount($adTag->getId()))
            ->setPosition($adTag->getPosition())
        ;

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function checkParameter($adTag)
    {
        return $adTag instanceof AdTagModelInterface;
    }
}