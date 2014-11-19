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

        return $this->getRatio($total, $totalWeight);
    }
}