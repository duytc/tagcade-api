<?php

namespace Tagcade\Domain\DTO\Report\Performance;


interface BaseAdTagReportCountInterface extends RedisReportDataInterface
{
    public function getFirstOpportunityCount();

    public function getOpportunities();

    public function getImpressions();

    public function getVerifiedImpressionCount();

    public function getPassbackCount();

    public function getUnverifiedImpressionCount();

    public function getBlankImpressionCount();

    public function getVoidImpressionCount();

    public function getClickCount();

    public function getForcedPassbacks();
} 