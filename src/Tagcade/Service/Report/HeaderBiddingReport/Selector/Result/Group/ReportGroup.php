<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Selector\Result\Group;

use ArrayIterator;
use DateTime;
use Tagcade\Model\Report\HeaderBiddingReport\ReportDataInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\HeaderBiddingReport\Selector\Result\ReportResultInterface;

class ReportGroup implements ReportDataInterface, ReportResultInterface
{
    protected $reportType;
    protected $reports;
    protected $name;
    protected $startDate;
    protected $endDate;

    protected $requests;
    protected $billedAmount;
    protected $billedRate;

    protected $averageRequests;
    protected $averageBilledAmount;

    /**
     * @param ReportTypeInterface|ReportTypeInterface[] $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param ReportDataInterface[] $reports
     * @param $name
     * @param int $requests
     * @param float $billedAmount
     * @param int $averageRequests
     * @param float $averageBilledAmount
     */
    public function __construct($reportType, DateTime $startDate, DateTime $endDate, array $reports, $name, $requests, $billedAmount, $averageRequests, $averageBilledAmount)
    {
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reports = $reports;
        $this->name = $name;
        $this->requests = $requests;
        $this->averageRequests = $averageRequests;

        $this->billedAmount = round($billedAmount, 4);
        $this->averageBilledAmount = round($averageBilledAmount, 4);
    }

    /**
     * @return ReportTypeInterface|ReportTypeInterface[]
     */
    public function getReportType()
    {
        return $this->reportType;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return ReportDataInterface[]
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }


    public function getIterator()
    {
        return new ArrayIterator($this->reports);
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
    public function getAverageBilledAmount()
    {
        return $this->averageBilledAmount;
    }

    /**
     * @return mixed
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * @param mixed $requests
     */
    public function setRequests($requests)
    {
        $this->requests = $requests;
    }

    /**
     * @return mixed
     */
    public function getBilledRate()
    {
        return $this->billedRate;
    }

    /**
     * @param mixed $billedRate
     */
    public function setBilledRate($billedRate)
    {
        $this->billedRate = $billedRate;
    }

    /**
     * @return mixed
     */
    public function getAverageRequests()
    {
        return $this->averageRequests;
    }

    /**
     * @param mixed $averageRequests
     */
    public function setAverageRequests($averageRequests)
    {
        $this->averageRequests = $averageRequests;
    }
}