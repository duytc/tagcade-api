<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Exception\LogicException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\PerformanceReport\CalculateEstCpmTrait;
use Tagcade\Model\Report\PerformanceReport\Display\Fields\SubReportsTrait;

abstract class AbstractCalculatedReport extends AbstractReport
{
    use SubReportsTrait;
    use CalculateEstCpmTrait;

    public function __construct()
    {
        $this->subReports = new ArrayCollection();
    }

    public function setCalculatedFields()
    {
        $this->doCalculateFields();

        parent::setCalculatedFields();
    }

    protected function doCalculateFields()
    {
        $this->totalOpportunities = 0;
        $this->impressions = 0;
        $this->passbacks = 0;
        $this->estRevenue = 0;

        foreach($this->subReports as $subReport) {
            /** @var ReportInterface $subReport */
            $subReport->setCalculatedFields(); // chain the calls to setCalculatedFields

            $this->aggregateSubReport($subReport);

            unset($subReport);
        }

        $this->setEstCpm($this->calculateEstCpm($this->getSubReports()));
    }

    protected function aggregateSubReport(ReportInterface $subReport)
    {
        $this->addTotalOpportunities($subReport->getTotalOpportunities());
        $this->addImpressions($subReport->getImpressions());
        $this->addPassbacks($subReport->getPassbacks());
        $this->addEstRevenue($subReport->getEstRevenue());
    }

    protected function addTotalOpportunities($totalOpportunities)
    {
        $this->totalOpportunities += (int)$totalOpportunities;
    }

    protected function addImpressions($impressions)
    {
        $this->impressions += (int)$impressions;
    }

    protected function addPassbacks($passbacks)
    {
        $this->passbacks += (int)$passbacks;
    }

    protected function addEstRevenue($estRevenue)
    {
        $this->estRevenue += (float)$estRevenue;
    }
}