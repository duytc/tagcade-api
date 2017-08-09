<?php

namespace Tagcade\Domain\DTO\Report\Performance;


interface BaseAdTagReportCountInterface extends RedisReportDataInterface
{
    /**
     * @return int
     */
    public function getFirstOpportunityCount();

    /**
     * @return int
     */
    public function getOpportunities();

    /**
     * @return int
     */
    public function getImpressions();

    /**
     * @return int
     */
    public function getVerifiedImpressionCount();

    /**
     * @return int
     */
    public function getPassbackCount();

    /**
     * @return int
     */
    public function getUnverifiedImpressionCount();

    /**
     * @return int
     */
    public function getBlankImpressionCount();

    /**
     * @return int
     */
    public function getVoidImpressionCount();

    /**
     * @return int
     */
    public function getClickCount();

    /**
     * @return int
     */
    public function getRefreshesCount();

    /**
     * @return int
     */
    public function getForcedPassbacks();
}