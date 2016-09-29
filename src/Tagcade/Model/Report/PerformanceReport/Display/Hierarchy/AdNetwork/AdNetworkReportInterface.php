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

    public function getAdNetworkId();

    /**
     * @return mixed
     */
    public function getBlankImpressions();

    public function setBlankImpressions($blankImpressions);

    /**
     * @return mixed
     */
    public function getFirstOpportunities();

    public function setFirstOpportunities($firstOpportunities);

    /**
     * @return mixed
     */
    public function getUnverifiedImpressions();

    public function setUnverifiedImpressions($unverifiedImpressions);

    /**
     * @return mixed
     */
    public function getVerifiedImpressions();

    public function setVerifiedImpressions($verifiedImpressions);

    /**
     * @return mixed
     */
    public function getClicks();

    public function setClicks($clicks);

    /**
     * @return mixed
     */
    public function getVoidImpressions();

    public function setVoidImpressions($voidImpressions);
}
