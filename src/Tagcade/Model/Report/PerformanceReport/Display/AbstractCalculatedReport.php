<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use Doctrine\Common\Collections\ArrayCollection;
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
}