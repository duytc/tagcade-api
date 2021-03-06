<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryDisplayAdSlotInterface extends BaseLibraryAdSlotInterface
{
    /**
     * @return int|null
     */
    public function getWidth();

    /**
     * @param int $width
     * @return self
     */
    public function setWidth($width);

    /**
     * @return int|null
     */
    public function getHeight();

    /**
     * @param int $height
     * @return self
     */
    public function setHeight($height);

    /**
     * @return boolean
     */
    public function isAutoFit();

    /**
     * @param boolean $autoFit
     */
    public function setAutoFit($autoFit);

    /**
     * @return string
     */
    public function getPassbackMode();

    /**
     * @param string $passbackMode
     * @return self
     */
    public function setPassbackMode($passbackMode);

    /**
     * @return PublisherInterface
     */
    public function getPublisher();

    /**
     * @param PublisherInterface $publisher
     * @return self
     */
    public function setPublisher(PublisherInterface $publisher);

    /**
     * @return mixed
     */
    public function getPublisherId();


    /**
     * @return DisplayAdSlotInterface[]
     */
    public function getDisplayAdSlots();

    /**
     * @return float
     */
    public function getBuyPrice();

    /**
     * @param float $buyPrice
     * @return self
     */
    public function setBuyPrice($buyPrice);
}