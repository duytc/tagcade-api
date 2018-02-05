<?php

namespace Tagcade\Model\Report;

use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;

trait CalculateSupplyCostTrait
{
    /**
     * @param $slotOpportunities
     * @param $refreshedSlotOpportunities
     * @param $buyPrice
     * @return float
     */
    protected function calculateSupplyCost($slotOpportunities, $refreshedSlotOpportunities, $buyPrice)
    {
        $slotOpportunities = empty($slotOpportunities) ? 0 : floatval($slotOpportunities);
        $refreshedSlotOpportunities = empty($refreshedSlotOpportunities) ? 0 : floatval($refreshedSlotOpportunities);
        $buyPrice = empty($buyPrice) ? 0 : floatval($buyPrice);

        $supplyCost = ($slotOpportunities - $refreshedSlotOpportunities) * $buyPrice / 1000;

        return $supplyCost;
    }

    /**
     * @param $adSlot
     * @return float|int
     */
    public function getAdSlotBuyPrice($adSlot)
    {
        if ($adSlot instanceof BaseAdSlotInterface) {
            $libraryAdSlot = $adSlot->getLibraryAdSlot();

            if ($libraryAdSlot instanceof LibraryDisplayAdSlotInterface) {
                return $libraryAdSlot->getBuyPrice();
            }

            if ($libraryAdSlot instanceof LibraryNativeAdSlotInterface) {
                return $libraryAdSlot->getBuyPrice();
            }
        }

        return 0;
    }

    /**
     * @param $numerator
     * @param $denominator
     * @return float|null
     */
    abstract protected function getRatio($numerator, $denominator);
}