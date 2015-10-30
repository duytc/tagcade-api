<?php

namespace Tagcade\Service\Cdn;


use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;

interface CDNUpdaterInterface {

    /**
     * @param $adSlotId
     * @return true|false
     *
     * @throws \RuntimeException
     */
    public function pushAdSlot($adSlotId);

    /**
     * @param array $adSlots
     * @return int
     *
     * @throws \RuntimeException
     */
    public function pushMultipleAdSlots(array $adSlots);

    /**
     * @param $ronAdSlotId
     * @return true|false
     *
     * @throws \RuntimeException
     */
    public function pushRonSlot($ronAdSlotId);

    /**
     * @param array $ronSlots
     * @return int
     *
     * @throws \RuntimeException
     */
    public function pushMultipleRonSlots(array $ronSlots);

}