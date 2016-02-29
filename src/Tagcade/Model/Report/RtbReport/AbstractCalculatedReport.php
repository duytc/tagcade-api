<?php

namespace Tagcade\Model\Report\RtbReport;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Report\RtbReport\Fields\SubReportsTrait;

abstract class AbstractCalculatedReport extends AbstractReport
{
    use SubReportsTrait;

    public function __construct()
    {
        $this->subReports = new ArrayCollection();
    }

    public function setCalculatedFields()
    {
        $this->doCalculateFields();

        parent::setCalculatedFields();

        $this->postCalculateFields();
    }

    /**
     *  use if some fields need to be calculated base on calculated fields
     */
    protected function postCalculateFields()
    {
        // Empty for now since some reports in hierarchy Platform and AdNetwork don't require
    }

    protected function resetCounts()
    {
        $this->opportunities = 0;
        $this->impressions = 0;
    }

    protected function doCalculateFields()
    {
        $this->resetCounts();

        foreach($this->subReports as $subReport) {
            /** @var ReportInterface $subReport */
            $subReport->setCalculatedFields(); // chain the calls to setCalculatedFields

            $this->aggregateSubReport($subReport);

            unset($subReport);
        }
    }

    protected function aggregateSubReport(ReportInterface $subReport)
    {
        $this->addOpportunities($subReport->getOpportunities());
        $this->addImpressions($subReport->getImpressions());
        $this->addEarnedAmount($subReport->getEarnedAmount());
    }

    protected function addOpportunities($opportunities)
    {
        $this->opportunities += (int)$opportunities;
    }

    protected function addImpressions($impressions)
    {
        $this->impressions += (int)$impressions;
    }

    protected function addEarnedAmount($earnedAmount)
    {
        $this->earnedAmount += (float)$earnedAmount;
    }
}