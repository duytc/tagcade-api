<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use Doctrine\Common\Collections\ArrayCollection;
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

        $ratio = $this->getRatio($this->getEstRevenue(), $this->getTotalOpportunities());

        if (!$ratio) {
            return 0;
        }

        return $ratio * 1000;
    }
}