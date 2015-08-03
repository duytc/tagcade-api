<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;

interface LibrarySlotTagInterface extends ModelInterface{

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
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     */
    public function setPosition($position);

    /**
     * @return string
     */
    public function getRefId();

    /**
     * @param string $refId
     */
    public function setRefId($refId);
}