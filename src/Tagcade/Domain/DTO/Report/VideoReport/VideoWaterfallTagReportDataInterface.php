<?php


namespace Tagcade\Domain\DTO\Report\VideoReport;


interface VideoWaterfallTagReportDataInterface extends VideoRedisReportDataInterface
{
    /**
     * @return int
     */
    public function getVideoWaterfallTagId();
}