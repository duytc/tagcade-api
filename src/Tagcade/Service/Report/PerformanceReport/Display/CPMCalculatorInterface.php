<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;

use Tagcade\Model\Core\AdTagInterface;

interface CPMCalculatorInterface {

    /**
     * @param AdTagInterface $adTag
     * @param int $opportunities
     * @return float
     */
    public function calculateCPM(AdTagInterface $adTag, $opportunities);
} 