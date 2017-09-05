<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Report\PerformanceReport\Display\BaseAdTagReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface AdTagReportInterface extends BaseAdTagReportInterface
{
    /**
     * @return AdTagInterface|null
     */
    public function getAdTag();

    /**
     * @return int|null
     */
    public function getAdTagId();

    /**
     * @return SubPublisherInterface
     */
    public function getSubPublisher();

    /**
     * @return int
     */
    public function getSubPublisherId();

    /**
     * @return int
     */
    public function getBlankImpressions();

    /**
     * @param int $blankImpressions
     * @return self
     */
    public function setBlankImpressions($blankImpressions);

    /**
     * @return int
     */
    public function getFirstOpportunities();

    /**
     * @param int $firstOpportunities
     * @return self
     */
    public function setFirstOpportunities($firstOpportunities);

    /**
     * @return int
     */
    public function getUnverifiedImpressions();

    /**
     * @param int $unverifiedImpressions
     * @return self
     */
    public function setUnverifiedImpressions($unverifiedImpressions);

    /**
     * @return int
     */
    public function getVerifiedImpressions();

    /**
     * @param int $verifiedImpressions
     * @return self
     */
    public function setVerifiedImpressions($verifiedImpressions);

    /**
     * @return int
     */
    public function getClicks();

    /**
     * @param int $clicks
     * @return self
     */
    public function setClicks($clicks);

    /**
     * @return int
     */
    public function getVoidImpressions();

    /**
     * @param int $voidImpressions
     * @return self
     */
    public function setVoidImpressions($voidImpressions);

    /**
     * @return mixed
     */
    public function getRefreshes();

    /**
     * @param $refreshes
     * @return self
     */
    public function setRefreshes($refreshes);

    /**
     * @return float
     */
    public function getNetworkOpportunityFillRate();

    /**
     * @param float $networkOpportunityFillRate
     * @return self
     */
    public function setNetworkOpportunityFillRate($networkOpportunityFillRate);
}