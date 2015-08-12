<?php

namespace Tagcade\Model\Core;


class LibrarySlotTag implements LibrarySlotTagInterface{

    protected $id;
    /**
     * @var LibraryAdTagInterface
     */
    protected $libraryAdTag;

    /**
     * @var BaseLibraryAdSlotInterface
     */
    protected $libraryAdSlot;

    protected $deletedAt;

    /**
     * @var boolean
     */
    protected $active = true;

    /**
     * @var integer
     */
    protected $frequencyCap;

    /**
     * @var integer
     */
    protected $rotation;

    /**
     * @var integer
     */
    protected $position;


    /**
     * @var string
     */
    protected $refId;

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return int
     */
    public function getFrequencyCap()
    {
        return $this->frequencyCap;
    }

    /**
     * @param int $frequencyCap
     */
    public function setFrequencyCap($frequencyCap)
    {
        $this->frequencyCap = $frequencyCap;
    }

    /**
     * @return int
     */
    public function getRotation()
    {
        return $this->rotation;
    }

    /**
     * @param int $rotation
     */
    public function setRotation($rotation)
    {
        $this->rotation = $rotation;
    }

    /**
     * @return string
     */
    public function getRefId()
    {
        return $this->refId;
    }

    /**
     * @param string $refId
     */
    public function setRefId($refId)
    {
        $this->refId = $refId;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return LibraryAdTagInterface
     */
    public function getLibraryAdTag()
    {
        return $this->libraryAdTag;
    }

    /**
     * @param LibraryAdTagInterface $libraryAdTag
     */
    public function setLibraryAdTag($libraryAdTag)
    {
        $this->libraryAdTag = $libraryAdTag;
    }

    /**
     * @return BaseLibraryAdSlotInterface
     */
    public function getLibraryAdSlot()
    {
        return $this->libraryAdSlot;
    }

    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     */
    public function setLibraryAdSlot($libraryAdSlot)
    {
        $this->libraryAdSlot = $libraryAdSlot;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }



    public function __toString()
    {
        return $this->id . $this->libraryAdSlot->getId() . $this->libraryAdTag->getId();
    }

}