<?php


namespace Tagcade\Domain\DTO\Report\VideoReport;


interface VideoRedisReportDataInterface
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
     * @return int
     */
    public function getClicks();

    /**
     * @return int
     */
    public function getBids();

    /**
     * @return int
     */
    public function getErrors();

    /**
     * @return int
     */
    public function getBlocks();
}