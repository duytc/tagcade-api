<?php

namespace Tagcade\Service\Report\VideoReport\Selector\Result\Group;

use ArrayIterator;
use Tagcade\Model\Report\VideoReport\ReportDataInterface;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Selector\Result\GroupedDataInterface;

class ReportGroup implements ReportDataInterface, GroupedDataInterface
{
    protected $reportType;
    protected $reports;
    protected $requests;
    protected $bids;
    protected $bidRate;
    protected $errors;
    protected $errorRate;
    protected $impressions;
    protected $requestFillRate;
    protected $clicks;
    protected $clickThroughRate;
    protected $estDemandRevenue;

    protected $averageRequests;
    protected $averageBids;
    protected $averageBidRate;
    protected $averageErrors;
    protected $averageErrorRate;
    protected $averageImpressions;
    protected $averageRequestFillRate;
    protected $averageClicks;
    protected $averageClickThroughRate;
    protected $startDate;
    protected $endDate;
    protected $blocks;
    protected $averageBlocks;
    protected $averageEstDemandRevenue;

    /**
     * ReportGroup constructor.
     * @param $reportType
     * @param array $reports
     * @param $requests
     * @param $bids
     * @param $bidRate
     * @param $errors
     * @param $errorRate
     * @param $impressions
     * @param $requestFillRate
     * @param $clicks
     * @param $clickThroughRate
     * @param $averageRequests
     * @param $averageBids
     * @param $averageBidRate
     * @param $averageErrors
     * @param $averageErrorRate
     * @param $averageImpressions
     * @param $averageRequestFillRate
     * @param $averageClicks
     * @param $averageClickThroughRate
     * @param $startDate
     * @param $endDate
     * @param $blocks
     * @param $averageBlocks
     * @param $estDemandRevenue
     * @param $averageEstDemandRevenue
     */
    public function __construct($reportType, array $reports,
                                $requests,
                                $bids,
                                $bidRate,
                                $errors,
                                $errorRate,
                                $impressions,
                                $requestFillRate,
                                $clicks,
                                $clickThroughRate,
                                $averageRequests,
                                $averageBids,
                                $averageBidRate,
                                $averageErrors,
                                $averageErrorRate,
                                $averageImpressions,
                                $averageRequestFillRate,
                                $averageClicks,
                                $averageClickThroughRate,
                                $startDate,
                                $endDate,
                                $blocks,
                                $averageBlocks,
                                $estDemandRevenue,
                                $averageEstDemandRevenue
    )
    {
        $this->reportType = $reportType;
        $this->reports = $reports;
        $this->requests = $requests;
        $this->bids = $bids;
        $this->bidRate = round($bidRate, 4);
        $this->errors = $errors;
        $this->errorRate = round($errorRate, 4);
        $this->impressions = $impressions;
        $this->requestFillRate = round($requestFillRate, 4);
        $this->clicks = $clicks;
        $this->clickThroughRate = round($clickThroughRate, 4);
        $this->estDemandRevenue = round($estDemandRevenue, 2);

        $this->averageRequests = round($averageRequests);
        $this->averageBids = round($averageBids);
        $this->averageBidRate = round($averageBidRate, 4);
        $this->averageErrors = round($averageErrors);
        $this->averageErrorRate = round($averageErrorRate, 4);
        $this->averageImpressions = round($averageImpressions);
        $this->averageRequestFillRate = round($averageRequestFillRate, 4);
        $this->averageClicks = round($averageClicks);
        $this->averageClickThroughRate = round($averageClickThroughRate, 4);
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->blocks = $blocks;
        $this->averageBlocks = round($averageBlocks);
        $this->averageEstDemandRevenue = round($averageEstDemandRevenue, 2);
    }

    /**
     * @return ReportTypeInterface|ReportTypeInterface[]
     */
    public function getReportType()
    {
        return $this->reportType;
    }

    /**
     * @return ReportDataInterface[]
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
     * @return int
     */
    public function getImpressions()
    {
        return $this->impressions;
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
     * @return float
     */
    public function getBidRate()
    {
        return $this->bidRate;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return float
     */
    public function getErrorRate()
    {
        return $this->errorRate;
    }

    /**
     * @return float
     */
    public function getRequestFillRate()
    {
        return $this->requestFillRate;
    }

    /**
     * @return mixed
     */
    public function getClicks()
    {
        return $this->clicks;
    }

    /**
     * @return float
     */
    public function getClickThroughRate()
    {
        return $this->clickThroughRate;
    }

    /**
     * @return float
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
     * @return float
     */
    public function getAverageBidRate()
    {
        return $this->averageBidRate;
    }

    /**
     * @return float
     */
    public function getAverageErrors()
    {
        return $this->averageErrors;
    }

    /**
     * @return float
     */
    public function getAverageErrorRate()
    {
        return $this->averageErrorRate;
    }

    /**
     * @return float
     */
    public function getAverageImpressions()
    {
        return $this->averageImpressions;
    }

    /**
     * @return float
     */
    public function getAverageRequestFillRate()
    {
        return $this->averageRequestFillRate;
    }

    /**
     * @return float
     */
    public function getAverageClicks()
    {
        return $this->averageClicks;
    }

    /**
     * @return float
     */
    public function getAverageClickThroughRate()
    {
        return $this->averageClickThroughRate;
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

    /**
     * @param $reports
     * @return mixed
     */
    public function setReports($reports)
    {
        $this->reports = $reports;
    }

    /**
     * @return int
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * @return mixed
     */
    public function getAverageBlocks()
    {
        return $this->averageBlocks;
    }

    public function getEstDemandRevenue()
    {
        return $this->estDemandRevenue;
    }

    /**
     * @return float
     */
    public function getAverageEstDemandRevenue()
    {
        return $this->averageEstDemandRevenue;
    }
}