<?php

namespace Tagcade\Service\TagLibrary;


use Tagcade\Model\Core\BaseAdSlotInterface;

interface ChecksumValidatorInterface {
    /**
     * @param BaseAdSlotInterface $originalAdSlot
     */
    public function validateAdSlotSynchronization(BaseAdSlotInterface $originalAdSlot);

    /**
     * @param array $adSlots
     * @return mixed
     */
    public function validateAllAdSlotsSynchronized(array $adSlots);

}