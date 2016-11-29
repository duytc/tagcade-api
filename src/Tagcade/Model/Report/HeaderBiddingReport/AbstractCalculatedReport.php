<?php

namespace Tagcade\Model\Report\HeaderBiddingReport;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Report\HeaderBiddingReport\Fields\SubReportsTrait;
use Tagcade\Model\Report\PerformanceReport\CalculateWeightedValueTrait;

abstract class AbstractCalculatedReport extends AbstractReport
{
    use SubReportsTrait;
    use CalculateWeightedValueTrait;

    public function __construct()
    {
        $this->subReports = new ArrayCollection();
    }

    public function setCalculatedFields($chainToSubReports = true)
    {
        $this->doCalculateFields($chainToSubReports);

        parent::setCalculatedFields();

        $this->postCalculateFields();
    }

    protected function resetCounts()
    {
        $this->requests = 0;
    }

    protected function postCalculateFields()
    {
        $this->setWeightedBilledRate();
    }

    protected function setWeightedBilledRate()
    {
        $weightedCpmRate = $this->calculateWeightedValue($this->getSubReports(), 'billedRate', 'billedAmount');
        $this->setBilledRate($weightedCpmRate);
    }

    protected function doCalculateFields($chainToSubReports = true)
    {
        $this->resetCounts();

        foreach($this->subReports as $subReport) {
            if ($chainToSubReports === true) {
                /** @var ReportInterface $subReport */
                $subReport->setCalculatedFields($chainToSubReports); // chain the calls to setCalculatedFields
            }

            $this->aggregateSubReport($subReport);

            unset($subReport);
        }
    }

    protected function aggregateSubReport(ReportInterface $subReport)
    {
        $this->addRequests($subReport->getRequests());
        $this->addBilledAmount($subReport->getBilledAmount());
    }

    protected function addRequests($requests)
    {
        $this->requests += (int)$requests;
    }

    protected function addBilledAmount($billedAmount)
    {
        $this->billedAmount += (float) $billedAmount;
    }
}