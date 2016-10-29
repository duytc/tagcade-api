<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Result\Group;
use Tagcade\Service\Report\VideoReport\Selector\Result\CalculatedReportGroupInterface;
use Tagcade\Model\Report\VideoReport\ReportDataInterface;
use Tagcade\Service\Report\VideoReport\Selector\Result\ReportResultInterface;

class WaterfallTagReportGroup extends ReportGroup implements CalculatedReportGroupInterface, ReportDataInterface, ReportResultInterface
{
    protected $adTagRequests;
    protected $adTagBids;
    protected $adTagErrors;
    protected $billedAmount;
    protected $estSupplyCost;
    protected $netRevenue;


    protected $averageAdTagRequest;
    protected $averageAdTagBid;
    protected $averageAdTagError;
    protected $averageBilledAmount;
    protected $billedRate;
    protected $averageEstSupplyCost;
    protected $averageNetRevenue;

    /**
     * WaterfallTagReportGroup constructor.
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
     * @param $adTagRequests
     * @param $adTagBids
     * @param $adTagErrors
     * @param $averageAdTagRequest
     * @param $averageAdTagBid
     * @param $averageAdTagError
     * @param $billedAmount
     * @param $averageBilledAmount
     * @param $billedRate
     * @param $estDemandRevenue
     * @param $averageDemandRevenue
     * @param $estSupplyCost
     * @param $averageSupplyCost
     * @param $netRevenue
     * @param $averageNetRevenue
     */
    public function __construct($reportType, array $reports, $requests, $bids, $bidRate, $errors, $errorRate, $impressions, $requestFillRate, $clicks,
        $clickThroughRate, $averageRequests, $averageBids, $averageBidRate, $averageErrors, $averageErrorRate, $averageImpressions, $averageRequestFillRate,
        $averageClicks, $averageClickThroughRate, $startDate, $endDate, $blocks, $averageBlocks, $adTagRequests, $adTagBids, $adTagErrors, $averageAdTagRequest, $averageAdTagBid,
        $averageAdTagError, $billedAmount, $averageBilledAmount, $billedRate, $estDemandRevenue, $averageDemandRevenue, $estSupplyCost, $averageSupplyCost, $netRevenue, $averageNetRevenue)
    {
        parent::__construct($reportType, $reports, $requests, $bids, $bidRate, $errors, $errorRate, $impressions, $requestFillRate,
            $clicks, $clickThroughRate, $averageRequests, $averageBids, $averageBidRate, $averageErrors, $averageErrorRate,
            $averageImpressions, $averageRequestFillRate, $averageClicks, $averageClickThroughRate,$startDate, $endDate, $blocks, $averageBlocks, $estDemandRevenue, $averageDemandRevenue);

        $this->adTagRequests = $adTagRequests;
        $this->adTagBids = $adTagBids;
        $this->adTagErrors = $adTagErrors;
        $this->billedAmount = round($billedAmount, 2);
        $this->estSupplyCost = round($estSupplyCost, 2);
        $this->netRevenue = round($netRevenue, 2);

        $this->averageAdTagRequest = round($averageAdTagRequest);
        $this->averageAdTagBid = round($averageAdTagBid);
        $this->averageAdTagError = round($averageAdTagError);
        $this->averageBilledAmount = round($averageBilledAmount, 2);
        $this->averageEstSupplyCost = round($averageSupplyCost, 2);
        $this->averageNetRevenue = round($averageNetRevenue, 2);
        $this->billedRate = round($billedRate, 4);
    }

    /**
     * @return mixed
     */
    public function getAdTagRequests()
    {
        return $this->adTagRequests;
    }

    /**
     * @return mixed
     */
    public function getAdTagBids()
    {
        return $this->adTagBids;
    }

    /**
     * @return mixed
     */
    public function getAdTagErrors()
    {
        return $this->adTagErrors;
    }

    /**
     * @return mixed
     */
    public function getAverageAdTagRequest()
    {
        return $this->averageAdTagRequest;
    }

    /**
     * @return mixed
     */
    public function getAverageAdTagBid()
    {
        return $this->averageAdTagBid;
    }

    /**
     * @return mixed
     */
    public function getAverageAdTagError()
    {
        return $this->averageAdTagError;
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
     * @return float|null
     */
    public function getAverageBilledAmount()
    {
        return $this->averageBilledAmount;
    }

    /**
     * @return float|null
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     * @return float|null
     */
    public function getBilledRate()
    {
        return $this->billedRate;
    }

    public function getEstSupplyCost()
    {
        return $this->estSupplyCost;
    }

    /**
     * @return mixed
     */
    public function getNetRevenue()
    {
        return $this->netRevenue;
    }

    /**
     * @return mixed
     */
    public function getAverageNetRevenue()
    {
        return $this->averageNetRevenue;
    }

    /**
     * @return mixed
     */
    public function getAverageEstSupplyCost()
    {
        return $this->averageEstSupplyCost;
    }
}