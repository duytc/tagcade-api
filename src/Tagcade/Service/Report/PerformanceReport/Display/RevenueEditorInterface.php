<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;

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
}