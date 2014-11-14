<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;

use Tagcade\Model\Core\AdTagInterface;

class CPMCalculator implements CPMCalculatorInterface
{
    /**
     * @inheritdoc
     */
    public function calculateCPM(AdTagInterface $adTag, $opportunities)
    {
        return 5;
    }

}