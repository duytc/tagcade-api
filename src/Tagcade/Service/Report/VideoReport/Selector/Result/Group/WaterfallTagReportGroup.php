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

    protected $averageAdTagRequest;
    protected $averageAdTagBid;
    protected $averageAdTagError;
    protected $averageBilledAmount;
    protected $billedRate;

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
     */
    public function __construct($reportType, array $reports, $requests, $bids, $bidRate, $errors, $errorRate, $impressions, $requestFillRate, $clicks,
                                $clickThroughRate, $averageRequests, $averageBids, $averageBidRate, $averageErrors, $averageErrorRate, $averageImpressions, $averageRequestFillRate,
                                $averageClicks, $averageClickThroughRate, $startDate, $endDate, $blocks, $averageBlocks, $adTagRequests, $adTagBids, $adTagErrors, $averageAdTagRequest, $averageAdTagBid,
                                $averageAdTagError, $billedAmount, $averageBilledAmount, $billedRate)
    {
        parent::__construct($reportType, $reports, $requests, $bids, $bidRate, $errors, $errorRate, $impressions, $requestFillRate,
            $clicks, $clickThroughRate, $averageRequests, $averageBids, $averageBidRate, $averageErrors, $averageErrorRate,
            $averageImpressions, $averageRequestFillRate, $averageClicks, $averageClickThroughRate,$startDate, $endDate, $blocks, $averageBlocks);

        $this->adTagRequests = isset($adTagRequests)? round($adTagRequests, 4) : null;
        $this->adTagBids = isset($adTagBids)? round($adTagBids, 4) : null;
        $this->adTagErrors = isset($adTagErrors)? round($adTagErrors, 4): null;
        $this->billedAmount = isset($billedAmount)? round($billedAmount,4) : null;

        $this->averageAdTagRequest = isset($averageAdTagRequest)? round($averageAdTagRequest, 4): null;
        $this->averageAdTagBid = isset($averageAdTagBid)? round($averageAdTagBid, 4):null;
        $this->averageAdTagError = isset($averageAdTagError)?round($averageAdTagError, 4):null;
        $this->averageBilledAmount = isset($averageBilledAmount) ? round($averageBilledAmount,4): null;

        $this->billedRate = isset($billedRate)? round($billedRate, 4) : null;
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
}