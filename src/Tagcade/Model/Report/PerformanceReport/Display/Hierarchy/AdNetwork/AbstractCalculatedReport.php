<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use Tagcade\Model\Report\PerformanceReport\Display\AbstractCalculatedReport as BaseAbstractCalculatedReport;
use Tagcade\Model\Report\PerformanceReport\Display\Fields\ImpressionBreakdownTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ImpressionBreakdownReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Exception\RuntimeException;

abstract class AbstractCalculatedReport extends BaseAbstractCalculatedReport implements ReportInterface
{
    use ImpressionBreakdownTrait;

    /**
     * @inheritdoc
     */
    protected function calculateFillRate()
    {
        if ($this->getTotalOpportunities() === null) {
            throw new RuntimeException('total opportunities must be defined to calculate fill rates');
        }

        return $this->getPercentage($this->getImpressions(), $this->getTotalOpportunities());
    }

    protected function aggregateSubReport(ReportInterface $subReport)
    {
        parent::aggregateSubReport($subReport);

        if (!$subReport instanceof ImpressionBreakdownReportDataInterface) {
            return;
        }

        $this->addFirstOpportunities($subReport->getFirstOpportunities());
        $this->addVerifiedImpressions($subReport->getVerifiedImpressions());
        $this->addUnverifiedImpressions($subReport->getUnverifiedImpressions());
        $this->addBlankImpressions($subReport->getBlankImpressions());
    }

    protected function addFirstOpportunities($firstOpportunities)
    {
        $this->firstOpportunities += (int)$firstOpportunities;
    }

    protected function addVerifiedImpressions($verifiedImpressions)
    {
        $this->verifiedImpressions += (int)$verifiedImpressions;
    }

    protected function addUnverifiedImpressions($unverifiedImpressions)
    {
        $this->unverifiedImpressions += (int)$unverifiedImpressions;
    }

    protected function addBlankImpressions($blankImpressions)
    {
        $this->blankImpressions += (int)$blankImpressions;
    }
}