<?php

namespace Tagcade\Domain\DTO\Report\RtbReport;


interface RtbAdSlotReportCountInterface extends RtbRedisReportDataInterface
{
    /**
     * @return int
     */
    public function getAdSlotId();
} 