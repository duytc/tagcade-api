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

    public function getSubPublisherId();

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