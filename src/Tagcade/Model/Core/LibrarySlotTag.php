<?php

namespace Tagcade\Model\Core;

use Tagcade\Entity\Core\LibrarySlotTag as entity;

class LibrarySlotTag implements LibrarySlotTagInterface, RonAdTagInterface{

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
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
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
     * @return $this
     */
    public function setFrequencyCap($frequencyCap)
    {
        $this->frequencyCap = $frequencyCap;

        return $this;
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
     * @return $this
     */
    public function setRotation($rotation)
    {
        $this->rotation = $rotation;

        return $this;
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
     * @return $this
     */
    public function setRefId($refId)
    {
        $this->refId = $refId;

        return $this;
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
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
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
     * @return $this
     */
    public function setLibraryAdTag($libraryAdTag)
    {
        $this->libraryAdTag = $libraryAdTag;

        return $this;
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
     * @return $this
     */
    public function setLibraryAdSlot($libraryAdSlot)
    {
        $this->libraryAdSlot = $libraryAdSlot;

        return $this;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return bool
     */
    public function isInLibrary()
    {
        return true;
    }


    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Get then entity that contain current object
     * @return mixed
     */
    public function getContainer()
    {
        return $this->getLibraryAdSlot();
    }

    /**
     * Get those entities that belong to the same container with the current entity
     * @return mixed
     */
    public function getSiblings()
    {
        return $this->getLibraryAdSlot()->getLibSlotTags();
    }

    /**
     * @return null|AdNetworkInterface
     */
    public function getAdNetwork() {
        if ($this->libraryAdTag instanceof LibraryAdTagInterface) {
            return $this->libraryAdTag->getAdNetwork();
        }

        return null;
    }

    /**
     * @return string
     */
    public function checkSum()
    {
        return  md5(serialize(
            array(
                $this->getLibraryAdTag()->getId(),
                $this->getName(),
                $this->getPosition(),
                $this->isActive(),
                $this->getFrequencyCap(),
                $this->getRotation(),
                $this->getRefId()
            )));
    }

    /**
     * get 'name' of the entity
     *
     * @return string
     */
    protected function getName(){
        if($this->getLibraryAdTag() === null) return null;

        return $this->getLibraryAdTag()->getName();
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return entity::class;
    }


    public function __toString()
    {
        return $this->id . $this->libraryAdSlot->getId() . $this->libraryAdTag->getId();
    }

}