<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\SiteInterface;

interface RevenueEditorInterface {
    /**
     * @param AdTagInterface $adTag
     * @param float $cpmRate
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return self
     */
    public function updateRevenueForAdTag(AdTagInterface $adTag, $cpmRate, DateTime $startDate, DateTime $endDate = null);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param float $cpmRate
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return self
     */
    public function updateRevenueForAdNetwork(AdNetworkInterface $adNetwork, $cpmRate, DateTime $startDate, DateTime $endDate = null);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param SiteInterface $site
     * @param $cpmRate
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return self
     */
    public function updateRevenueForAdNetworkSite(AdNetworkInterface $adNetwork, SiteInterface $site, $cpmRate, DateTime $startDate, DateTime $endDate = null);
}