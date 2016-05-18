<?php

namespace Tagcade\Model\Core;

interface LibrarySlotTagInterface extends PositionInterface {

    /**
     * @return LibraryAdTagInterface
     */
    public function getLibraryAdTag();

    /**
     * @param LibraryAdTagInterface $libraryAdTag
     * @return self
     */
    public function setLibraryAdTag($libraryAdTag);

    /**
     * @return BaseLibraryAdSlotInterface
     */
    public function getLibraryAdSlot();

    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @return self
     */
    public function setLibraryAdSlot($libraryAdSlot);


    /**
     * @return boolean
     */
    public function isActive();

    /**
     * @param boolean $active
     * @return self
     */
    public function setActive($active);
    /**
     * @return int
     */
    public function getFrequencyCap();

    /**
     * @param int $frequencyCap
     * @return self
     */
    public function setFrequencyCap($frequencyCap);

    /**
     * @return int
     */
    public function getRotation();

    /**
     * @param int $rotation
     * @return self
     */
    public function setRotation($rotation);

    /**
     * @return int
     */
    public function getImpressionCap();

    /**
     * @param int $impressionCap
     * @return self
     */
    public function setImpressionCap($impressionCap);

    /**
     * @return int
     */
    public function getNetworkOpportunityCap();

    /**
     * @param int $networkOpportunityCap
     * @return self
     */
    public function setNetworkOpportunityCap($networkOpportunityCap);

    /**
     * @return null|AdNetworkInterface
     */
    public function getAdNetwork();

    /**
     * @return boolean
     */
    public function getAutoIncreasePosition();

    /**
     * @param bool $autoIncreasePosition
     * @return self
     */
    public function setAutoIncreasePosition($autoIncreasePosition);

    /**
     * @return string
     */
    public function checkSum();
}