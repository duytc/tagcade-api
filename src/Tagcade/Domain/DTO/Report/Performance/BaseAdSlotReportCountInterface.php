<?php

namespace Tagcade\Domain\DTO\Report\Performance;


interface BaseAdSlotReportCountInterface extends RedisReportDataInterface
{
    public function getSlotOpportunities();
} 