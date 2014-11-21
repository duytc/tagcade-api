<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;

use Tagcade\Model\Core\AdTagInterface;
use \DateTime;

class EstCpmCalculator implements EstCpmCalculatorInterface
{
    public function getEstCpmForAdTag(AdTagInterface $adTag, DateTime $date = null)
    {
        $estCpm = $adTag->getAdNetwork()->getDefaultCpmRate();

        if (!$estCpm) {
            return 0;
        }

        return $estCpm;
    }
}