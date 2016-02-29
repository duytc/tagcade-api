<?php

namespace Tagcade\Domain\DTO\Report\RtbReport;


interface RtbRonAdSlotReportCountInterface extends RtbRedisReportDataInterface
{
    /**
     * @return int
     */
    public function getRonAdSlotId();
} 