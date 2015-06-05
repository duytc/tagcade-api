<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

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

}