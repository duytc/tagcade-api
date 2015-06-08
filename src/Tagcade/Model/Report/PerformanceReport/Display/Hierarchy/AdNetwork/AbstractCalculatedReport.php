<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\PerformanceReport\Display\AbstractCalculatedReport as BaseAbstractCalculatedReport;
use Tagcade\Model\Report\PerformanceReport\Display\Fields\ImpressionBreakdownTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ImpressionBreakdownReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Exception\RuntimeException;

abstract class AbstractCalculatedReport extends BaseAbstractCalculatedReport implements ReportInterface, ImpressionBreakdownReportDataInterface
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

    protected function resetCounts()
    {
        parent::resetCounts();

        $this->firstOpportunities = 0;
        $this->verifiedImpressions = 0;
        $this->unverifiedImpressions = 0;
        $this->blankImpressions = 0;
        $this->voidImpressions = 0;
        $this->clicks = 0;
    }

    protected function aggregateSubReport(ReportInterface $subReport)
    {
        parent::aggregateSubReport($subReport);

        if (!$subReport instanceof ImpressionBreakdownReportDataInterface) {
            throw new LogicException('Expected a ImpressionBreakdownReportDataInterface');
        }

        $this->addFirstOpportunities($subReport->getFirstOpportunities());
        $this->addVerifiedImpressions($subReport->getVerifiedImpressions());
        $this->addUnverifiedImpressions($subReport->getUnverifiedImpressions());
        $this->addBlankImpressions($subReport->getBlankImpressions());
        $this->addVoidImpressions($subReport->getVoidImpressions());
        $this->addClicks($subReport->getClicks());
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

    protected function addVoidImpressions($voidImpressions)
    {
        $this->voidImpressions += (int)$voidImpressions;
    }

    protected function addClicks($clicks)
    {
        $this->clicks += (int)$clicks;
    }
}