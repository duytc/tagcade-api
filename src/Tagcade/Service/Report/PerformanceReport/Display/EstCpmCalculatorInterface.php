<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use DateTime;

interface EstCpmCalculatorInterface {

    /**
     * @param AdTagInterface $adTag
     * @param int $opportunities
     * @return float
     */
    public function calculateRevenue(AdTagInterface $adTag, $opportunities);

    /**
     * @param AdTagInterface $adTag
     * @param DateTime $date
     * @return float
     */
    public function getEstCpmForAdTag(AdTagInterface $adTag, DateTime $date = null);

    /**
     * @param AdTagInterface $adTag
     * @param $cpmRate
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return $this
     */
    public function updateCpmRateForAdTag(AdTagInterface $adTag, $cpmRate, DateTime $startDate, DateTime $endDate = null);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param $cpmRate
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return $this
     */
    public function updateCpmRateForAdNetwork(AdNetworkInterface $adNetwork, $cpmRate, DateTime $startDate, DateTime $endDate = null);
} 