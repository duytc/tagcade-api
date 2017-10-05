<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector\Grouper\Groupers;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Model\Report\CalculateRevenueTrait;
use Tagcade\Model\Report\PerformanceReport\CalculateAdOpportunitiesTrait;
use Tagcade\Model\Report\PerformanceReport\CalculateWeightedValueTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\ReportGroup;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportResultInterface;


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
    use CalculateWeightedValueTrait;
    use CalculateRevenueTrait;
    use CalculateAdOpportunitiesTrait;

    private $reportType;
    private $reports;
    private $reportName;
    private $startDate;
    private $endDate;
    private $totalOpportunities;
    private $impressions;
    private $passbacks;
    private $fillRate;
    private $estCpm;
    private $estRevenue;
    private $adOpportunities;
    private $inBannerImpressions;
    private $inBannerRequests;
    private $inBannerTimeouts;

    private $averageTotalOpportunities;
    private $averageImpressions;
    private $averagePassbacks;
    private $averageFillRate;
    private $averageEstCpm;
    private $averageEstRevenue;
    private $averageAdOpportunities;
    private $averageInBannerImpressions;
    private $averageInBannerRequests;
    private $averageInBannerTimeouts;

    private $totalEstCpm;
    private $totalFillRate;

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
        $this->reportName = $reportResult->getName();
        $this->startDate = $reportResult->getStartDate();
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
            $this->getReportName(),
            $this->getTotalOpportunities(),
            $this->getImpressions(),
            $this->getPassbacks(),
            $this->getFillRate(),
            $this->getEstCpm(),
            $this->getEstRevenue(),
            $this->getAdOpportunities(),
            $this->getAverageTotalOpportunities(),
            $this->getAverageImpressions(),
            $this->getAveragePassbacks(),
            $this->getAverageEstCpm(),
            $this->getAverageEstRevenue(),
            $this->getAverageFillRate(),
            $this->getAverageAdOpportunities(),
            $this->getInBannerImpressions(),
            $this->getInBannerRequests(),
            $this->getInBannerTimeouts(),
            $this->getAverageInBannerImpressions(),
            $this->getAverageInBannerRequests(),
            $this->getAverageInBannerTimeouts()
        );
    }

    /**
     * @param ReportDataInterface[] $reports
     */
    protected function groupReports(array $reports)
    {
        foreach ($reports as $report) {
            $this->doGroupReport($report);
        }

        $this->setFillRate();
        $this->estCpm = $this->getRatio($this->getEstRevenue() * 1000, $this->getImpressions());
        $this->adOpportunities = $this->calculateAdOpportunities($this->totalOpportunities, $this->passbacks);

        // Calculate average for totalOpportunities,impressions and passbacks
        $reportCount = count($this->getReports());
        $this->averageTotalOpportunities = $this->getRatio($this->getTotalOpportunities(), $reportCount);
        $this->averageImpressions = $this->getRatio($this->getImpressions(), $reportCount);
        $this->averagePassbacks = $this->getRatio($this->getPassbacks(), $reportCount);
        $this->averageEstCpm = $this->getRatio($this->getTotalEstCpm(), $reportCount);
        $this->averageFillRate = $this->getRatio($this->getTotalFillRate(), $reportCount);
        $this->averageEstRevenue = $this->getRatio($this->getEstRevenue(), $reportCount);
        $this->averageAdOpportunities = $this->getRatio($this->getAdOpportunities(), $reportCount);
        $this->averageInBannerImpressions = $this->getRatio($this->getInBannerImpressions(), $reportCount);
        $this->averageInBannerRequests = $this->getRatio($this->getInBannerRequests(), $reportCount);
        $this->averageInBannerTimeouts = $this->getRatio($this->getInBannerTimeouts(), $reportCount);
    }

    protected function doGroupReport(ReportDataInterface $report)
    {
        $this->addImpressions($report->getImpressions());
        $this->addTotalOpportunities($report->getTotalOpportunities());
        $this->addPassbacks($report->getPassbacks());
        $this->addTotalEstCpm($report->getEstCpm());
        $this->addEstRevenue($report->getEstRevenue());
        $this->addTotalFillRate($report->getFillRate());
        $this->addInBannerImpressions($report->getInBannerImpressions());
        $this->addInBannerRequests($report->getInBannerRequests());
        $this->addInBannerTimeouts($report->getInBannerTimeouts());
    }

    protected function addTotalOpportunities($totalOpportunities)
    {
        $this->totalOpportunities += (int)$totalOpportunities;
    }

    protected function addImpressions($impressions)
    {
        $this->impressions += (int)$impressions;
    }

    protected function addPassbacks($passbacks)
    {
        $this->passbacks += (int)$passbacks;
    }

    protected function addEstRevenue($estRevenue)
    {
        $this->estRevenue += (float)$estRevenue;
    }

    protected function addTotalEstCpm($estCpm)
    {
        $this->totalEstCpm += (float)$estCpm;
    }

    protected function addTotalFillRate($fillRate)
    {
        $this->totalFillRate += (float)$fillRate;
    }

    protected function addInBannerImpressions($inBannerImpressions)
    {
        $this->inBannerImpressions += (int)$inBannerImpressions;
    }

    protected function addInBannerRequests($inBannerRequests)
    {
        $this->inBannerRequests += (int)$inBannerRequests;
    }

    protected function addInBannerTimeouts($inBannerTimeouts)
    {
        $this->inBannerTimeouts += (int)$inBannerTimeouts;
    }

    protected function setFillRate()
    {
        $this->fillRate = $this->calculateFillRate();
    }

    protected function calculateFillRate()
    {
        return $this->getPercentage($this->getImpressions(), $this->getTotalOpportunities());
    }

    public function getReportType()
    {
        return $this->reportType;
    }

    public function getReports()
    {
        return $this->reports;
    }

    public function getReportName()
    {
        return $this->reportName;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function getTotalOpportunities()
    {
        return $this->totalOpportunities;
    }

    public function getImpressions()
    {
        return $this->impressions;
    }

    public function getInBannerImpressions()
    {
        return $this->inBannerImpressions;
    }

    public function getInBannerRequests()
    {
        return $this->inBannerRequests;
    }

    public function getInBannerTimeouts()
    {
        return $this->inBannerTimeouts;
    }

    public function getPassbacks()
    {
        return $this->passbacks;
    }

    public function getFillRate()
    {
        return $this->fillRate;
    }

    public function getAverageTotalOpportunities()
    {
        return $this->averageTotalOpportunities;
    }

    public function getAverageImpressions()
    {
        return $this->averageImpressions;
    }

    public function getAveragePassbacks()
    {
        return $this->averagePassbacks;
    }

    public function getEstCpm()
    {
        return $this->estCpm;
    }

    public function getEstRevenue()
    {
        return $this->estRevenue;
    }

    public function getAdOpportunities()
    {
        return $this->adOpportunities;
    }

    public function getAverageEstCpm()
    {
        return $this->averageEstCpm;
    }

    public function getAverageEstRevenue()
    {
        return $this->averageEstRevenue;
    }

    public function getTotalEstCpm()
    {
        return $this->totalEstCpm;
    }

    public function getAverageFillRate()
    {
        return $this->averageFillRate;
    }

    public function getTotalFillRate()
    {
        return $this->totalFillRate;
    }

    public function getAverageAdOpportunities()
    {
        return $this->averageAdOpportunities;
    }

    public function getAverageInBannerImpressions()
    {
        return $this->averageInBannerImpressions;
    }

    public function getAverageInBannerRequests()
    {
        return $this->averageInBannerRequests;
    }

    public function getAverageInBannerTimeouts()
    {
        return $this->averageInBannerTimeouts;
    }

    /**
     * @param mixed $totalOpportunities
     * @return self
     */
    public function setTotalOpportunities($totalOpportunities)
    {
        $this->totalOpportunities = $totalOpportunities;
        return $this;
    }

    /**
     * @param mixed $impressions
     * @return self
     */
    public function setImpressions($impressions)
    {
        $this->impressions = $impressions;
        return $this;
    }

    /**
     * @param mixed $passbacks
     * @return self
     */
    public function setPassbacks($passbacks)
    {
        $this->passbacks = $passbacks;
        return $this;
    }

    /**
     * @param mixed $estCpm
     * @return self
     */
    public function setEstCpm($estCpm)
    {
        $this->estCpm = $estCpm;
        return $this;
    }

    /**
     * @param mixed $estRevenue
     * @return self
     */
    public function setEstRevenue($estRevenue)
    {
        $this->estRevenue = $estRevenue;
        return $this;
    }

    /**
     * @param int $adOpportunities
     */
    public function setAdOpportunities($adOpportunities)
    {
        $this->adOpportunities = $adOpportunities;
    }

    /**
     * @param mixed $averageTotalOpportunities
     * @return self
     */
    public function setAverageTotalOpportunities($averageTotalOpportunities)
    {
        $this->averageTotalOpportunities = $averageTotalOpportunities;
        return $this;
    }

    /**
     * @param mixed $averageImpressions
     * @return self
     */
    public function setAverageImpressions($averageImpressions)
    {
        $this->averageImpressions = $averageImpressions;
        return $this;
    }

    /**
     * @param mixed $averagePassbacks
     * @return self
     */
    public function setAveragePassbacks($averagePassbacks)
    {
        $this->averagePassbacks = $averagePassbacks;
        return $this;
    }

    /**
     * @param mixed $averageFillRate
     * @return self
     */
    public function setAverageFillRate($averageFillRate)
    {
        $this->averageFillRate = $averageFillRate;
        return $this;
    }

    /**
     * @param mixed $averageEstCpm
     * @return self
     */
    public function setAverageEstCpm($averageEstCpm)
    {
        $this->averageEstCpm = $averageEstCpm;
        return $this;
    }

    /**
     * @param mixed $averageEstRevenue
     * @return self
     */
    public function setAverageEstRevenue($averageEstRevenue)
    {
        $this->averageEstRevenue = $averageEstRevenue;
        return $this;
    }

    /**
     * @param float $averageAdOpportunities
     */
    public function setAverageAdOpportunities($averageAdOpportunities)
    {
        $this->averageAdOpportunities = $averageAdOpportunities;
    }
}