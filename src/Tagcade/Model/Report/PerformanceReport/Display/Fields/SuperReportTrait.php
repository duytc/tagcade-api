<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Fields;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

trait SuperReportTrait
{
    /**
     * @var ReportInterface
     */
    protected $superReport;

    /**
     * The super report is the report that 'owns' this report
     *
     * i.e a SiteReport owns many AdSlotReports
     */
    public function getSuperReport()
    {
        return $this->superReport;
    }

    public function getSuperReportId()
    {
        if ($this->superReport instanceof ReportInterface) {
            return $this->superReport->getId();
        }

        return null;
    }

    /**
     * @param ReportInterface $report
     * @return bool
     */
    abstract public function isValidSuperReport(ReportInterface $report);

    /**
     * @param ReportInterface $report
     * @return static
     */
    public function setSuperReport(ReportInterface $report)
    {
        if (!$this->isValidSuperReport($report)) {
            throw new InvalidArgumentException('That super report is not valid for this report');
        }

        $this->superReport = $report;

        return $this;
    }
}