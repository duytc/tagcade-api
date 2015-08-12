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
    protected $position;
    protected $active;
    protected $frequencyCap;

    /** int - for rotation display AdTags */
    protected $rotation;
    protected $deletedAt;


    /**
     * @var LibraryAdTagInterface
     */
    protected $libraryAdTag;
    protected $refId;
    /**
     * @param LibraryAdTagInterface $libraryAdTag
     */
    public function __construct(LibraryAdTagInterface $libraryAdTag = null)
    {
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
     * @return $this;
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
        if($this->libraryAdTag === null) return null;

        return $this->libraryAdTag->getAdNetwork();
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
        if($this->libraryAdTag != null) {
            $this->libraryAdTag->setAdNetwork($adNetwork);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        if($this->libraryAdTag instanceof LibraryAdTagInterface)
        {
            return $this->libraryAdTag->getName();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        if($this->libraryAdTag instanceof LibraryAdTagInterface)
        {
            return $this->libraryAdTag->setName($name);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHtml()
    {
        if($this->libraryAdTag == null) return null;

        return $this->libraryAdTag->getHtml();
    }

    /**
     * @inheritdoc
     */
    public function setHtml($html)
    {
        if($this->libraryAdTag != null) {
            $this->libraryAdTag->setHtml($html);
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

        return $this;
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
    public function setLibraryAdTag($libraryAdTag)
    {
        $this->libraryAdTag = $libraryAdTag;

        return $this;
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
     * @return mixed
     */
    public function getRefId()
    {
        return $this->refId;
    }

    /**
     * @param mixed $refId
     * @return $this;
     */
    public function setRefId($refId)
    {
        $this->refId = $refId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
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

    public function __toString()
    {
        return $this->id . $this->getName();
    }

}