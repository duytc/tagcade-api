<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\RootReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SuperReportInterface;

interface AdNetworkReportInterface extends CalculatedReportInterface, RootReportInterface, SuperReportInterface
{
    /**
     * @return AdNetworkInterface
     */
    public function getAdNetwork();

    /**
     * @return int
     */
    public function getAdNetworkId();

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
     * @return mixed
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
     * @return float
     */
    public function getNetworkOpportunityFillRate();

    /**
     * @param float $networkOpportunityFillRate
     * @return self
     */
    public function setNetworkOpportunityFillRate($networkOpportunityFillRate);
}