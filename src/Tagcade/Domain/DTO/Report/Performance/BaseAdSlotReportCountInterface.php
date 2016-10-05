<?php

namespace Tagcade\Domain\DTO\Report\Performance;


interface BaseAdSlotReportCountInterface extends RedisReportDataInterface
{
    public function getSlotOpportunities();

    public function getRtbImpression();

    public function getHbRequests();
}