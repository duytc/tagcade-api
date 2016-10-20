<?php


namespace Tagcade\Model\Report\VideoReport;


interface ReportDataInterface
{
    /**
     * @return int
     */
    public function getRequests();

    /**
     * @return int
     */
    public function getImpressions();

    /**
     * @return float
     */
    public function getRequestFillRate();

    /**
     * @return int
     */
    public function getErrors();

    /**
     * @return float
     */
    public function getErrorRate();

    /**
     * @return int
     */
    public function getClicks();

    /**
     * @return float
     */
    public function getClickThroughRate();

    /**
     * @return int
     */
    public function getBids();

    /**
     * @return float
     */
    public function getBidRate();

    /**
     * @return int
     */
    public function getBlocks();
}