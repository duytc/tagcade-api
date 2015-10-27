<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;

use DateTime;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;

interface EstCpmCalculatorInterface {

    /**
     * @param AdTagInterface|LibrarySlotTagInterface $adTag
     * @param DateTime $date
     * @return float
     */
    public function getEstCpmForAdTag($adTag, DateTime $date = null);

}