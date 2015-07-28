<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;

class AdTag implements AdTagInterface
{
    protected $id;

    /**
     * @var BaseAdSlotInterface
     */
    protected $adSlot;
    protected $name;
    protected $position;
    protected $active;
    protected $frequencyCap;

    /** int - for rotation display AdTags */
    protected $rotation;


    /**
     * @var LibraryAdTagInterface
     */
    protected $libraryAdTag;
    protected $refId;
    /**
     * @param $name
     * @param LibraryAdTagInterface $libraryAdTag
     */
    public function __construct($name,  LibraryAdTagInterface $libraryAdTag = null)
    {
        $this->name = $name;
        $this->html = $libraryAdTag->getHtml();

        if ($libraryAdTag) {
            $this->setAdNetwork($libraryAdTag->getAdNetwork());
        }
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function getAdSlot()
    {
        return $this->adSlot;
    }

    /**
     * @inheritdoc
     */
    public function getAdSlotId()
    {
        if (!$this->adSlot) {
            return null;
        }

        return $this->adSlot->getId();
    }

    public function setAdSlot(BaseAdSlotInterface $adSlot)
    {
        $this->adSlot = $adSlot;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAdNetwork()
    {

        /**
         * @var LibraryAdTagInterface $libraryAdTag
         */
        $libraryAdTag = $this->getLibraryAdTag();

        if($libraryAdTag == null) return null;

        return $libraryAdTag->getAdNetwork();
    }

    /**
     * @inheritdoc
     */
    public function getAdNetworkId()
    {
        $libraryAdTag = $this->getLibraryAdTag();

        if($libraryAdTag == null) return null;

        /**
         * @var AdNetworkInterface $adNetwork
         */
        $adNetwork =  $libraryAdTag->getAdNetwork();

        if($adNetwork == null) return null;

        return $adNetwork->getId();
    }

    public function setAdNetwork(AdNetworkInterface $adNetwork)
    {
        /**
         * @var LibraryAdTagInterface $libraryAdTag
         */
        $libraryAdTag = $this->getLibraryAdTag();

        if($libraryAdTag != null) {
            $libraryAdTag->setAdNetwork($adNetwork);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHtml()
    {
        /**
         * @var LibraryAdTagInterface $libraryAdTag
         */
        $libraryAdTag = $this->getLibraryAdTag();

        if($libraryAdTag == null) return null;

        return $libraryAdTag->getHtml();
    }

    /**
     * @inheritdoc
     */
    public function setHtml($html)
    {
        /**
         * @var LibraryAdTagInterface $libraryAdTag
         */
        $libraryAdTag = $this->getLibraryAdTag();

        if($libraryAdTag != null) {
            $libraryAdTag->setHtml($html);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @inheritdoc
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @inheritdoc
     */
    public function setActive($boolean)
    {
        $this->active = (Boolean)$boolean;
        return $this;
    }

    /**
     * @param int|null $frequencyCap
     * @return $this
     */
    public function setFrequencyCap($frequencyCap)
    {
        $this->frequencyCap = $frequencyCap;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFrequencyCap()
    {
        return $this->frequencyCap;
    }

    /**
     * @inheritdoc
     */
    public function setRotation($rotation)
    {
        $this->rotation = $rotation;
    }

    /**
     * @inheritdoc
     */
    public function getRotation()
    {
        return $this->rotation;
    }

    /**
     * @inheritdoc
     */
    public function getLibraryAdTag()
    {
        return $this->libraryAdTag;
    }

    /**
     * @inheritdoc
     */
    public function setLibraryAdTag(LibraryAdTagInterface $libraryAdTag)
    {
        $this->libraryAdTag = $libraryAdTag;
    }

    /**
     * @return AdTagInterface[]
     */
    public function getCoReferencedAdTags()
    {
        if($this->getLibraryAdTag() == null) return new ArrayCollection();

        return $this->getLibraryAdTag()->getAdTags();
    }


    /**
     * Calculate CheckSum string of an given AdTag
     * by concatenating major properties together with null value ignored, then returning the MD5 hash
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
     * @return mixed
     */
    public function getRefId()
    {
        return $this->refId;
    }

    /**
     * @param mixed $refId
     */
    public function setRefId($refId)
    {
        $this->refId = $refId;
    }
}