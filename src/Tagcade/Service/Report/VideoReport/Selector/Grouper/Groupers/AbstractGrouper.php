<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Grouper\Groupers;


use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Model\Report\VideoReport\ReportDataInterface;
use Tagcade\Service\Report\VideoReport\Selector\Result\Group\ReportGroup;
use Tagcade\Service\Report\VideoReport\Selector\Result\ReportResultInterface;

abstract class AbstractGrouper
{
    use CalculateRatiosTrait;

    private $reportType;
    private $reports;
    private $startDate;
    private $endDate;

    private $requests;
    private $bids;
    private $bidRate;
    private $errors;
    private $errorRate;
    private $impressions;
    private $fillRate;
    private $clicks;
    private $clickThroughRate;
    private $blocks;

    private $averageRequests;
    private $averageBids;
    private $averageBidRate;
    private $averageErrors;
    private $averageErrorRate;
    private $averageImpressions;
    private $averageFillRate;
    private $averageClicks;
    private $averageClickThroughRate;
    private $averageBlocks;

    private $totalFillRate;
    private $totalErrorRate;
    private $totalBidRate;
    private $totalClickThroughRate;


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
        $this->reports = $reports;
        $this->startDate = $reportResult->getStartDate();
        $this->endDate  = $reportResult->getEndDate();

        $this->groupReports($reports);
    }

    public function getGroupedReport()
    {
        return new ReportGroup(
            $this->getReportType(),
            $this->getReports(),
            $this->getRequests(),
            $this->getBids(),
            $this->getBidRate(),
            $this->getErrors(),
            $this->getErrorRate(),
            $this->getImpressions(),
            $this->getFillRate(),
            $this->getClicks(),
            $this->getClickThroughRate(),
            $this->getAverageRequests(),
            $this->getAverageBids(),
            $this->getAverageBidRate(),
            $this->getAverageErrors(),
            $this->getAverageErrorRate(),
            $this->getAverageImpressions(),
            $this->getAverageFillRate(),
            $this->getAverageClicks(),
            $this->getAverageClickThroughRate(),
            $this->getStartDate(),
            $this->getEndDate(),
            $this->getBlocks(),
            $this->getAverageBlocks()
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

        $this->setFillRate()
            ->setBidRate()
            ->setClickThroughRate()
            ->setErrorRate()
        ;

        $reportCount = count($reports);
        $this->averageRequests = $this->getRatio($this->getRequests(), $reportCount);
        $this->averageImpressions = $this->getRatio($this->getImpressions(), $reportCount);
        $this->averageClicks = $this->getRatio($this->getClicks(), $reportCount);
        $this->averageBids = $this->getRatio($this->getBids(), $reportCount);
        $this->averageErrors = $this->getRatio($this->getErrors(), $reportCount);
        $this->averageBlocks = $this->getRatio($this->getBlocks(), $reportCount);

        $this->averageFillRate = $this->getRatio($this->getTotalFillRate(), $reportCount);
        $this->averageErrorRate = $this->getRatio($this->getTotalErrorRate(), $reportCount);
        $this->averageBidRate = $this->getRatio($this->getTotalBidRate(), $reportCount);
        $this->averageClickThroughRate = $this->getRatio($this->getTotalClickThroughRate(), $reportCount);

    }

    protected function getTotalDays(\DateTime $startDate, \DateTime $endDate)
    {
        return $startDate->diff($endDate)->format('%a');
    }

    protected function doGroupReport(ReportDataInterface $report)
    {
        $this->addRequests($report->getRequests());
        $this->addBids($report->getBids());
        $this->addBidRate($report->getBidRate());
        $this->addErrors($report->getErrors());
        $this->addErrorRate($report->getErrorRate());
        $this->addClicks($report->getClicks());
        $this->addClickThroughRate($report->getClickThroughRate());
        $this->addFillRate($report->getFillRate());
        $this->addImpressions($report->getImpressions());
        $this->addBlocks($report->getBlocks());
    }

    protected function addRequests($requests)
    {
        $this->requests += (int) $requests;
    }

    protected function addImpressions($impressions)
    {
        $this->impressions += (int) $impressions;
    }

    protected function addFillRate($fillRate)
    {
        $this->totalFillRate += (float) $fillRate;
    }

    protected function addBids($bids)
    {
        $this->bids += (int) $bids;
    }

    protected function addBidRate($bidRate)
    {
        $this->totalBidRate += (float) $bidRate;
    }

    protected function addErrors($errors)
    {
        $this->errors += (int) $errors;
    }

    protected function addErrorRate($errorRate)
    {
        $this->totalErrorRate += (float) $errorRate;
    }

    protected function addClicks($clicks)
    {
        $this->clicks += (int) $clicks;
    }

    protected function addClickThroughRate($clickThroughRate)
    {
        $this->totalClickThroughRate += (float) $clickThroughRate;
    }

    protected function addBlocks($blocks)
    {
        $this->blocks += (int) $blocks;
    }

    public function getReportType()
    {
        return $this->reportType;
    }

    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @return mixed
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * @return mixed
     */
    public function getBids()
    {
        return $this->bids;
    }

    /**
     * @return mixed
     */
    public function getBidRate()
    {
        return $this->bidRate;
    }

    /**
     * @return mixed
     */
    protected function getTotalBidRate()
    {
        return $this->totalBidRate;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return mixed
     */
    public function getErrorRate()
    {
        return $this->errorRate;
    }

    /**
     * @return mixed
     */
    protected function getTotalErrorRate()
    {
        return $this->totalErrorRate;
    }

    /**
     * @return mixed
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @return mixed
     */
    public function getFillRate()
    {
        return $this->fillRate;
    }

    /**
     * @return mixed
     */
    protected function getTotalFillRate()
    {
        return $this->totalFillRate;
    }

    /**
     * @return mixed
     */
    public function getClicks()
    {
        return $this->clicks;
    }

    /**
     * @return mixed
     */
    public function getClickThroughRate()
    {
        return $this->clickThroughRate;
    }

    /**
     * @return mixed
     */
    protected function getTotalClickThroughRate()
    {
        return $this->totalClickThroughRate;
    }

    /**
     * @return mixed
     */
    public function getAverageRequests()
    {
        return $this->averageRequests;
    }

    /**
     * @return mixed
     */
    public function getAverageBids()
    {
        return $this->averageBids;
    }

    /**
     * @return mixed
     */
    public function getAverageBidRate()
    {
        return $this->averageBidRate;
    }

    /**
     * @return mixed
     */
    public function getAverageErrors()
    {
        return $this->averageErrors;
    }

    /**
     * @return mixed
     */
    public function getAverageErrorRate()
    {
        return $this->averageErrorRate;
    }

    /**
     * @return mixed
     */
    public function getAverageImpressions()
    {
        return $this->averageImpressions;
    }

    /**
     * @return mixed
     */
    public function getAverageFillRate()
    {
        return $this->averageFillRate;
    }

    /**
     * @return mixed
     */
    public function getAverageClicks()
    {
        return $this->averageClicks;
    }

    /**
     * @return mixed
     */
    public function getAverageClickThroughRate()
    {
        return $this->averageClickThroughRate;
    }

    /**
     * @return $this
     */
    protected function setFillRate()
    {
        $this->fillRate = $this->getRatio($this->getImpressions(), $this->getRequests());
        return $this;
    }

    /**
     * @return $this
     */
    protected function setErrorRate()
    {
        $this->errorRate = $this->getRatio($this->getErrors(), $this->getBids());
        return $this;
    }

    /**
     * @return $this
     */
    protected function setClickThroughRate()
    {
        $this->clickThroughRate = $this->getRatio($this->getClicks(), $this->getImpressions());
        return $this;
    }

    /**
     * @return $this
     */
    protected function setBidRate()
    {
        $this->bidRate = $this->getRatio($this->getBids(), $this->getRequests());
        return $this;
    }

    /**
     * @return mixed
     */
    protected function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return mixed
     */
    protected  function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return mixed
     */
    public function getAverageBlocks()
    {
        return $this->averageBlocks;
    }

    /**
     * @return mixed
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

}