<?php

namespace Tagcade\Model\Report;

use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;

trait CalculateRevenueTrait
{
    /**
     * @param $adOpportunities
     * @param $sellPrice
     * @return float
     */
    protected function calculateEstRevenue($adOpportunities, $sellPrice)
    {
        $adOpportunities = empty($adOpportunities) ? 0 : floatval($adOpportunities);
        $sellPrice = empty($sellPrice) ? 0 : floatval($sellPrice);

        $estRevenue = $adOpportunities * $sellPrice / 1000;

        return $estRevenue;
    }

    /**
     * @param $adTag
     * @return float|int
     */
    public function getAdTagSellPrice($adTag)
    {
        if ($adTag instanceof AdTagInterface &&
            $adTag->getLibraryAdTag() instanceof LibraryAdTagInterface
        ) {
            return $adTag->getLibraryAdTag()->getSellPrice();
        }

        return 0;
    }
}