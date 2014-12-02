<?php

namespace Tagcade\Domain\DTO\Statistics;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\CalculatedReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class DaySummary
{
    /**
     * @var CalculatedReportInterface
     */
    protected $report;

    // these variables make serializer overwrite value in report variable for output with rounding up to 4 digits after decimal point.
    protected $billedRate;
    protected $billedAmount;
    protected $estCpm;
    protected $estRevenue;


    function __construct(ReportInterface $report = null)
    {
        $this->report = $report;

        if ($report !=null) {
            $this->billedRate = round($this->report->getBilledRate(), 4);
            $this->billedAmount = round($this->report->getBilledAmount(), 4);
            $this->estCpm = round($this->report->getEstCpm(), 4);
            $this->estRevenue = round($this->report->getEstRevenue(), 4);
        }
    }

    /**
     * @return ReportInterface
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @return float
     */
    public function getBilledRate()
    {
        return $this->billedRate;
    }

    /**
     * @return float
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     * @return float
     */
    public function getEstCpm()
    {
        return $this->estCpm;
    }

    /**
     * @return float
     */
    public function getEstRevenue()
    {
        return $this->estRevenue;
    }
}