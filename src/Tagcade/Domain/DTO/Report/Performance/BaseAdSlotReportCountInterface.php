<?php

namespace Tagcade\Domain\DTO\Report\Performance;


interface BaseAdSlotReportCountInterface extends RedisReportDataInterface
{
    public function getSlotOpportunities();

    public function getHbRequests();

    public function getInBannerRequests();

    public function getInBannerTimeouts();

    public function getInBannerImpressions();
}