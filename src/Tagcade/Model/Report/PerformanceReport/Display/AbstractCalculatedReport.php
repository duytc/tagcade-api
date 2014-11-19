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

    abstract protected function doCalculateFields();

    protected function calculateEstCpm()
    {
        $estRevenue = $this->getEstRevenue();
        $totalOpportunities = $this->getTotalOpportunities();

        if ($estRevenue === null || $totalOpportunities === null) {
            throw new RuntimeException('cannot calculate estCpm, missing data');
        }

        $estCpm = $this->getRatio($this->getEstRevenue() * 1000, $this->getTotalOpportunities());

        if (!$estCpm) {
            return 0;
        }

        return $estCpm;
    }

    protected function getWeightedEstCpm()
    {
        /**
         * @var ReportInterface $report
         */
        $total = 0;
        $totalWeight = 0;

        foreach($this->getSubReports() as $report) {
            $number = &$report->getEstCpm();
            $weight = &$report->getEstRevenue();

            $total += $number * $weight;
            $totalWeight += $weight;
        }

        if ($totalWeight == 0) {
            return null;
        }

        return $total / $totalWeight;
    }
}