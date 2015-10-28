<?php

namespace Tagcade\Service\Cdn;


use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;

interface CDNUpdaterInterface {

    public function pushAdSlot($adSlotId);

    public function pushMultipleAdSlots(array $adSlots);

    public function pushRonSlot($ronAdSlotId);

    public function pushMultipleRonSlots(array $ronSlots);

}