<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use DateTime;

interface EstCpmCalculatorInterface {

    /**
     * @param AdTagInterface $adTag
     * @param DateTime $date
     * @return float
     */
    public function getEstCpmForAdTag(AdTagInterface $adTag, DateTime $date = null);

}