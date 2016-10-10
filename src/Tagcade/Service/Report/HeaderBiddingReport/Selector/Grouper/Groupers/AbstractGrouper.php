<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Selector\Grouper\Groupers;

use Tagcade\Model\Report\HeaderBiddingReport\ReportDataInterface;
use Tagcade\Service\Report\HeaderBiddingReport\Selector\Result\ReportResultInterface;
use Tagcade\Model\Report\CalculateRevenueTrait;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Service\Report\HeaderBiddingReport\Selector\Result\Group\ReportGroup;


/**
 * A grouper is only designed to be run once, if you need to group multiple sets of reports
 * you need to create a new grouper for each
 *
 * if you use this service in the symfony DI container you need to change the mode since
 * by default symfony creates 1 instance of a service and returns it each time.
 *
 * For this service, we need to create a new one and return it every time the service is requested
 */

abstract class AbstractGrouper implements GrouperInterface
{
    use CalculateRatiosTrait;
    use CalculateRevenueTrait;

    private $reportType;
    private $reports;
    private $startDate;
    private $endDate;
    private $name;

    private $billedAmount;
    private $billedRate;
    private $requests;

    private $averageBilledAmount;
    private $averageBilledRate;
    private $averageRequests;

    /**
     * @param ReportResultInterface $reportResult
     */
    public function __construct(ReportResultInterface $reportResult)
    {
        $reports = $reportResult->getReports();

        if (empty($reports)) {
            throw new InvalidArgumentException('Expected a non-empty array of reports');
        }

        $this->reportType = $reportResult->getReportType();
        $this->startDate = $reportResult->getStartDate();
        $this->name = $reportResult->getName();
        $this->endDate = $reportResult->getEndDate();
        $this->reports = $reports;

        $this->groupReports($reports);
    }

    public function getGroupedReport()
    {
        return new ReportGroup(
            $this->getReportType(),
            $this->getStartDate(),
            $this->getEndDate(),
            $this->getReports(),
            $this->getName(),
            $this->getRequests(),
            $this->getBilledAmount(),
            $this->getAverageRequests(),
            $this->getAverageBilledAmount()
        );
    }

    /**
     * @param ReportDataInterface[] $reports
     */
    protected function groupReports(array $reports)
    {
        foreach($reports as $report) {
            $this->doGroupReport($report);
        }

        // Calculate average for billedAmount, requests
        $reportCount = count($this->getReports());

        $this->averageBilledAmount = $this->getRatio($this->getBilledAmount(), $reportCount);
        $this->averageRequests = $this->getRatio($this->getRequests(), $reportCount);
    }

    protected function doGroupReport(ReportDataInterface $report)
    {
        $this->addBilledAmount($report->getBilledAmount());
        $this->addBilledRate($report->getBilledRate());
        $this->addRequests($report->getRequests());
    }

    protected function addRequests($requests)
    {
        $this->requests += (int) $requests;
    }

    protected function addBilledRate($billedRate)
    {
        $this->billedRate += (float) $billedRate;
    }

    protected function addBilledAmount($billedAmount)
    {
        $this->billedAmount += (float) $billedAmount;
    }

    public function getReportType()
    {
        return $this->reportType;
    }

    public function getReports()
    {
        return $this->reports;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     * @param mixed $billedAmount
     */
    public function setBilledAmount($billedAmount)
    {
        $this->billedAmount = $billedAmount;
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
    public function getAverageBilledAmount()
    {
        return $this->averageBilledAmount;
    }

    /**
     * @param mixed $averageBilledAmount
     */
    public function setAverageBilledAmount($averageBilledAmount)
    {
        $this->averageBilledAmount = $averageBilledAmount;
    }

    /**
     * @return mixed
     */
    public function getAverageBilledRate()
    {
        return $this->averageBilledRate;
    }

    /**
     * @param mixed $averageBilledRate
     */
    public function setAverageBilledRate($averageBilledRate)
    {
        $this->averageBilledRate = $averageBilledRate;
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