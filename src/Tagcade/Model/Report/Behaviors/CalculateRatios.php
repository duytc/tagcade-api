<?php

namespace Tagcade\Model\Report\Behaviors;

trait CalculateRatios
{
    /**
     * @param $numerator
     * @param $denominator
     * @return float|null
     */
    protected function getRatio($numerator, $denominator)
    {
        $ratio = null;

        if (is_numeric($denominator) && $denominator > 0 && is_numeric($numerator)) {
            $ratio = $numerator / $denominator;
        }

        return $ratio;
    }

    /**
     * @param $numerator
     * @param $denominator
     * @return float|null
     */
    protected function getPercentage($numerator, $denominator)
    {
        $ratio = $this->getRatio($numerator, $denominator);

        if (null == $ratio) {
            return null;
        }

        if ($ratio > 1.00) {
            $ratio = 1.00;
        }

        return $ratio;
    }
}