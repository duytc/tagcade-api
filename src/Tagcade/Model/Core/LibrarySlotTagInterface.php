<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;

interface LibrarySlotTagInterface extends PositionInterface {

    /**
     * @return LibraryAdTagInterface
     */
    public function getLibraryAdTag();

    /**
     * @param LibraryAdTagInterface $libraryAdTag
     */
    public function setLibraryAdTag($libraryAdTag);

    /**
     * @return BaseLibraryAdSlotInterface
     */
    public function getLibraryAdSlot();

    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     */
    public function setLibraryAdSlot($libraryAdSlot);


    /**
     * @return boolean
     */
    public function isActive();

    /**
     * @param boolean $active
     */
    public function setActive($active);
    /**
     * @return int
     */
    public function getFrequencyCap();

    /**
     * @param int $frequencyCap
     */
    public function setFrequencyCap($frequencyCap);

    /**
     * @return int
     */
    public function getRotation();

    /**
     * @param int $rotation
     */
    public function setRotation($rotation);

    /**
     * @return string
     */
    public function checkSum();
}