<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;

use DateTime;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;

class EstCpmCalculator implements EstCpmCalculatorInterface
{
    public function getEstCpmForAdTag( $adTag, DateTime $date = null)
    {
        if (!$adTag instanceof AdTagInterface && !$adTag instanceof LibrarySlotTagInterface) {
            throw new InvalidArgumentException('expect an AdTagInterface object or LibrarySlotTagInterface object');
        }

        $estCpm = $adTag->getAdNetwork()->getDefaultCpmRate();

        if (!$estCpm) {
            return 0;
        }

        return $estCpm;
    }
}