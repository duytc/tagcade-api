<?php

namespace Tagcade\Model\Report;

trait CalculateComparisonRatiosTrait
{
    /**
     * @param $numerator
     * @param $denominator
     * @return float|null
     */
    public function getComparisonRatio($numerator, $denominator)
    {
        $ratio = null;
        if ($denominator === 0) {
            return 1;
        }

        if (is_numeric($denominator) && $denominator > 0 && is_numeric($numerator)) {
            $ratio = $numerator / $denominator - 1;
        }

        return $ratio;
    }

    /**
     * @param $numerator
     * @param $denominator
     * @return float
     */
    public function getComparisonPercentage($numerator, $denominator)
    {
        $ratio = $this->getComparisonRatio($numerator, $denominator);

        if (null == $ratio) {
            return 0.00;
        }

        return round($ratio, 4);
    }
}