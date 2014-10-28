<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Fields;

use Tagcade\Exception\InvalidArgumentException;

use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Report\PerformanceReport\Display\SubReportInterface;

trait SubReportsTrait
{
    /**
     * @var ArrayCollection
     */
    protected $subReports;

    public function getSubReports()
    {
        return $this->subReports;
    }

    public function addSubReport(ReportInterface $report)
    {
        if (!$this->isValidSubReport($report)) {
            throw new InvalidArgumentException('That sub report is not valid for this report');
        }

        $report->setDate($this->getDate());

        if ($report instanceof SubReportInterface) {
            $report->setSuperReport($this);
        }

        $this->subReports->add($report);

        return $this;
    }

    abstract public function isValidSubReport(ReportInterface $report);

    /**
     * @return \DateTime
     */
    abstract public function getDate();
}