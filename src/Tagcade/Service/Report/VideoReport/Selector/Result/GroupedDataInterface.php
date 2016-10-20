<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Result;


interface GroupedDataInterface extends ReportResultInterface
{
    /**
     * @return float
     */
    public function getAverageRequests();

    /**
     * @return mixed
     */
    public function getAverageBids();

    /**
     * @return float
     */
    public function getAverageBidRate();

    /**
     * @return float
     */
    public function getAverageErrors();

    /**
     * @return float
     */
    public function getAverageErrorRate();

    /**
     * @return float
     */
    public function getAverageImpressions();

    /**
     * @return float
     */
    public function getAverageRequestFillRate();

    /**
     * @return float
     */
    public function getAverageClicks();

    /**
     * @return float
     */
    public function getAverageClickThroughRate();

    /**
     * @param $reports
     * @return mixed
     */
    public function setReports($reports);

}