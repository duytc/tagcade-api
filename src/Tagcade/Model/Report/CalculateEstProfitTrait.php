<?php

namespace Tagcade\Model\Report;

trait CalculateEstProfitTrait
{
    /**
     * @param $estRevenue
     * @param $supplyCost
     * @return float
     */
    protected function calculateEstProfit($estRevenue, $supplyCost)
    {
        $estRevenue = empty($estRevenue) ? 0 : floatval($estRevenue);
        $supplyCost = empty($supplyCost) ? 0 : floatval($supplyCost);

        $estProfit = $estRevenue - $supplyCost;

        return $estProfit;
    }

    /**
     * @param $numerator
     * @param $denominator
     * @return float|null
     */
    abstract protected function getRatio($numerator, $denominator);
}