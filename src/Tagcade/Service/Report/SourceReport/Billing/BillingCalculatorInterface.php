<?php

namespace Tagcade\Service\Report\SourceReport\Billing;


use DateTime;
use Tagcade\Model\Core\SiteInterface;

interface BillingCalculatorInterface
{
    /**
     * @param DateTime $date
     * @param SiteInterface $site
     * @param $module
     * @param $newWeight
     * @return mixed
     */
    public function calculateBilledAmountForSiteForSingleDate(DateTime $date, SiteInterface $site, $module, $newWeight);
}