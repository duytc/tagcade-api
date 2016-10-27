<?php


namespace Tagcade\Model\Report\VideoReport;


interface AdTagReportDataInterface extends ReportDataInterface
{
    /**
     * @return int
     */
    public function getAdTagRequests();

    /**
     * @return int
     */
    public function getAdTagBids();

    /**
     * @return int
     */
    public function getAdTagErrors();

    /**
     * @return float
     */
    public function getBilledAmount();

    /**
     * @return float
     */
    public function getBilledRate();

    /**
     * @return float
     */
    public function getEstSupplyCost();
}