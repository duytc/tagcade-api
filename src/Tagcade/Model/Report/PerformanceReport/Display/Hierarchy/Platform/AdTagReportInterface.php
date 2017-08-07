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
     * @return $this
     */
    public function setRelativeFillRate($totalOpportunities);

    /**
     * @return mixed
     */
    public function getRelativeFillRate();

    /**
     * @return mixed
     */
    public function getBlankImpressions();


    public function setBlankImpressions($blankImpressions);

    /**
     * @return mixed
     */
    public function getFirstOpportunities();

    /**
     * @param mixed $firstOpportunities
     */
    public function setFirstOpportunities($firstOpportunities);

    /**
     * @return mixed
     */
    public function getUnverifiedImpressions();
    /**
     * @param mixed $unverifiedImpressions
     */
    public function setUnverifiedImpressions($unverifiedImpressions);

    /**
     * @return mixed
     */
    public function getVerifiedImpressions();

    /**
     * @param mixed $verifiedImpressions
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
     * @return mixed
     */
    public function getClicks();

    public function setClicks($clicks);

    /**
     * @return mixed
     */
    public function getVoidImpressions();

    /**
     * @return mixed
     */
    public function getRefreshes();

    /**
     * @param $refreshes
     * @return self
     */
    public function setRefreshes($refreshes);
}