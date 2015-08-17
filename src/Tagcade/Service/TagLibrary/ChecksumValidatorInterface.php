<?php

namespace Tagcade\Service\TagLibrary;


use Tagcade\Model\Core\BaseAdSlotInterface;

interface ChecksumValidatorInterface {
    /**
     * @param BaseAdSlotInterface $originalAdSlot
     * @param null $copies
     */
    public function validateAdSlotSynchronization(BaseAdSlotInterface $originalAdSlot, $copies = null);

    /**
     * @param array $adSlots
     * @return mixed
     */
    public function validateAllAdSlotsSynchronized(array $adSlots);

}