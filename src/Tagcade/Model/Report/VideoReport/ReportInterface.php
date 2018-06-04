<?php


namespace Tagcade\Model\Report\VideoReport;

use DateTime;

interface ReportInterface extends ReportDataInterface
{
    /**
     * @param int $bids
     */
    public function setBids($bids);

    /**
     * @param int $clicks
     */
    public function setClicks($clicks);

    /**
     * @return DateTime|null
     */
    public function getDate();

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date = null);

    /**
     * @param int $errors
     */
    public function setErrors($errors);

    /**
     * @param int $impressions
     */
    public function setImpressions($impressions);

    /**
     * @param int $requests
     */
    public function setRequests($requests);

    /**
     * Sets all calculated fields
     * i.e fill rate
     */
    public function setCalculatedFields($chainToSubReports = true);

    /**
     * @return mixed
     */
    public function setClickThroughRate();

    /**
     * @return mixed
     */
    public function setBidRate();

    /**
     * @return mixed
     */
    public function setErrorRate();

    /**
     * @return mixed
     */
    public function setRequestFillRate();
    /**
     * @return mixed
     */
    public function getBlocks();

    /**
     * @param mixed $blocks
     */
    public function setBlocks($blocks);

    /**
     * @return mixed
     */
    public function getEstDemandRevenue();

    /**
     * @return self
     */
    public function setEstDemandRevenue();

    // support hourly data for video
    // no need to save subReport to Redis -> so we provide setSubReports method to reset subReport to []
    /**
     * @param $subReports
     */
    public function setSubReports($subReports);
}