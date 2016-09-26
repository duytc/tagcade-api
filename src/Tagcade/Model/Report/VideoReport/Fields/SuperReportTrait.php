<?php


namespace Tagcade\Model\Report\VideoReport\Fields;


use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\VideoReport\ReportInterface;

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