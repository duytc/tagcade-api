<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Result;


use ArrayIterator;
use Tagcade\Model\Report\VideoReport\ReportDataInterface;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;

class ReportCollection implements ReportResultInterface
{
    protected $reportType;
    protected $reports;
    protected $startDate;
    protected $endDate;

    /**
     * @param ReportTypeInterface|ReportTypeInterface[] $reportType
     * @param ReportDataInterface[] $reports
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     */
    public function __construct($reportType, array $reports, \DateTime $startDate, \DateTime $endDate)
    {
        $this->reportType = $reportType;
        $this->reports = $reports;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return mixed
     */
    public function getReportType()
    {
        return $this->reportType;
    }

    /**
     * @return mixed
     */
    public function getReports()
    {
        return $this->reports;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->reports);
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
}