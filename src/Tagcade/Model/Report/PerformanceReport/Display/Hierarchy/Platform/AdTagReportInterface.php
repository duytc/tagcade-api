<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Report\PerformanceReport\Display\BaseAdTagReportInterface;

interface AdTagReportInterface extends BaseAdTagReportInterface
{
    /**
     * To calculate the relative fill rate, the total opportunities from the entire ad slot must be supplied
     *
     * @param int $totalOpportunities
     * @return self
     */
    public function setRelativeFillRate($totalOpportunities);

    /**
     * @return mixed
     */
    public function getRelativeFillRate();

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
     * @return mixed
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
     * @return int|null
     */
    public function getPosition();

    /**
     * It is important to record the position of the ad tag on the day on this report
     * so we can show the correct ad tag order
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position);

    /**
     * @return AdTagInterface|null
     */
    public function getAdTag();

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
     * @return int
     */
    public function getRefreshes();

    /**
     * @param int $refreshes
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