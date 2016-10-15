<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Billing;


use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface ProjectedBillingCalculatorInterface
{

    /**
     * This will do calculation of projected billed amount for current month
     * @param PublisherInterface $publisher
     * @return float|bool projected billed amount or false on failure
     */
    public function calculateProjectedBilledAmountForPublisher(PublisherInterface $publisher);

    /**
     * This will do calculation of projected billed amount for site in current month
     * @param SiteInterface $site
     * @return mixed
     */
    public function calculateProjectedBilledAmountForSite(SiteInterface $site);

}