<?php

namespace Tagcade\Service\Report\SourceReport\Billing;


use DateTime;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface CpmRateGetterInterface
{
    /**
     * @param $slotOpportunities
     * @return mixed
     */
    public function getDefaultCpmRate($slotOpportunities);

    /**
     * @param SiteInterface $site
     * @param $module
     * @param DateTime $date
     * @return mixed
     */
    public function getBillingWeightForSiteInMonthBeforeDate(SiteInterface $site, $module, DateTime $date);

    /**
     * @param PublisherInterface $publisher
     * @param $module
     * @param $weight
     * @return mixed
     */
    public function getCpmRateForPublisher(PublisherInterface $publisher, $module, $weight);
}