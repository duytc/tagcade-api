<?php


namespace Tagcade\Domain\DTO\Report\VideoReport;


interface VideoDemandAdTagReportDataInterface extends VideoRedisReportDataInterface
{
    /**
     * @return int
     */
    public function getVideoDemandAdTagId();
}