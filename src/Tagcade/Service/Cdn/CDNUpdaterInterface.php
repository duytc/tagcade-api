<?php

namespace Tagcade\Service\Cdn;


use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;

interface CDNUpdaterInterface {

    /**
     * @param $adSlotId
     * @param $closeConnection
     * @return true|false
     *
     * @throws \RuntimeException
     */
    public function pushAdSlot($adSlotId, $closeConnection = true);

    /**
     * @param array $adSlots
     * @return int
     *
     * @throws \RuntimeException
     */
    public function pushMultipleAdSlots(array $adSlots);

    /**
     * @param $ronAdSlotId
     * @param $closeConnection
     * @return true|false
     *
     * @throws \RuntimeException
     */
    public function pushRonSlot($ronAdSlotId, $closeConnection = true);

    /**
     * @param array $ronSlots
     * @return int
     *
     * @throws \RuntimeException
     */
    public function pushMultipleRonSlots(array $ronSlots);

    /**
     * Close the current connection
     * @return mixed
     */
    public function closeFtpConnection();

}