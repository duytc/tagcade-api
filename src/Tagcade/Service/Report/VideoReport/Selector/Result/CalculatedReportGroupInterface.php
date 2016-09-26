<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Result;


interface CalculatedReportGroupInterface
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
     * @return mixed
     */
    public function getAverageAdTagRequest();

    /**
     * @return mixed
     */
    public function getAverageAdTagBid();

    /**
     * @return mixed
     */
    public function getAverageAdTagError();

    /**
     * @return float|null
     */
    public function getAverageBilledAmount();

    /**
     * @return float|null
     */
    public function getBilledAmount();

    /**
     * @return float|null
     */
    public function getBilledRate();

}