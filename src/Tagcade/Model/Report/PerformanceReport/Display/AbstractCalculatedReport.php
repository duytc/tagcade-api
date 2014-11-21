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
        $totalOpportunities = $impressions = $passbacks = $estRevenue = $billingCost = 0;

        foreach($this->subReports as $subReport) {
            /** @var ReportInterface $subReport */
            $subReport->setCalculatedFields(); // chain the calls to setCalculatedFields

            $totalOpportunities += $subReport->getTotalOpportunities();
            $impressions += $subReport->getImpressions();
            $passbacks += $subReport->getPassbacks();
            $estRevenue += $subReport->getEstRevenue();
            $billingCost += $subReport->getBillingCost();
            unset($subReport);
        }

        $this->setTotalOpportunities($totalOpportunities);
        $this->setImpressions($impressions);
        $this->setPassbacks($passbacks);
        $this->setEstRevenue($estRevenue);
        $this->setEstCpm($this->getWeightedEstCpm());
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