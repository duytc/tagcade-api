<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Exception\LogicException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\PerformanceReport\Display\Fields\SubReportsTrait;

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
    }

    protected function doCalculateFields()
    {
        $this->totalOpportunities = 0;
        $this->impressions = 0;
        $this->passbacks = 0;
        $this->estRevenue = 0;
        $this->billingCost = 0;

        foreach($this->subReports as $subReport) {
            /** @var ReportInterface $subReport */
            $subReport->setCalculatedFields(); // chain the calls to setCalculatedFields

            $this->aggregateSubReport($subReport);

            unset($subReport);
        }

        $this->setEstCpm($this->getWeightedEstCpm());
    }

    protected function aggregateSubReport(ReportInterface $subReport)
    {
        $this->addTotalOpportunities($subReport->getTotalOpportunities());
        $this->addImpressions($subReport->getImpressions());
        $this->addPassbacks($subReport->getPassbacks());
        $this->addEstRevenue($subReport->getEstRevenue());
        $this->addBillingCost($subReport->getBillingCost());
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

    protected function addBillingCost($billingCost)
    {
        $this->billingCost += (float)$billingCost;
    }

    protected function getWeightedEstCpm()
    {
        /**
         * @var ReportInterface $report
         */
        $total = 0;
        $totalWeight = 0;

        foreach($this->getSubReports() as $report) {
            $number = $report->getEstCpm();
            $weight = $report->getEstRevenue();

            $total += $number * $weight;
            $totalWeight += $weight;
        }

        return $this->getRatio($total, $totalWeight);
    }
}