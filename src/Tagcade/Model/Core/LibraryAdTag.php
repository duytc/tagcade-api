<?php

namespace Tagcade\Model\Core;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

class LibraryAdTag implements LibraryAdTagInterface
{
    const AD_TYPE_THIRD_PARTY = 0;
    const AD_TYPE_IMAGE = 1;
    const AD_TYPE_IN_BANNER = 2;

    protected $id;

    protected $html;

    protected $visible = false;

    /** int - type of AdTags*/
    protected $adType = 0;

    /** array - json_array, descriptor of WaterfallTag*/
    protected $descriptor;

    /** @var AdNetworkInterface */
    protected $adNetwork;

    /** @var Collection */
    protected $adTags;

    /** @var LibrarySlotTagInterface */
    protected $libSlotTags;

    protected $name;

    protected $partnerTagId;

    protected $platform;
    protected $timeout;
    protected $vastTags;
    protected $playerWidth;
    protected $playerHeight;

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @inheritdoc
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setAdNetwork($adNetwork)
    {
        $this->adNetwork = $adNetwork;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @return mixed
     */
    public function getPartnerTagId()
    {
        return $this->partnerTagId;
    }

    /**
     * @param mixed $partnerTagId
     * @return self
     */
    public function setPartnerTagId($partnerTagId)
    {
        $this->partnerTagId = $partnerTagId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAdTags()
    {
        if(null === $this->adTags) {
            $this->adTags = new ArrayCollection();
        }

        return $this->adTags;
    }

    public function addAdTag(AdTagInterface $adTag)
    {
        if( null === $this->adTags) {
            $this->adTags = new ArrayCollection();
        }

        $this->adTags->add($adTag);
        return $this;
    }


    /**
     * This indicate ad tag type: image, custom, etc..
     * get AdType
     * @return int
     */
    public function getAdType()
    {
        return $this->adType;
    }

    /**
     * @inheritdoc
     */
    public function setAdType($adType)
    {
        $this->adType = $adType;

        return $this;
    }

    /**
     * get Descriptor as json_array
     * @return array
     */
    public function getDescriptor()
    {
        return $this->descriptor;
    }

    /**
     * @inheritdoc
     */
    public function setDescriptor($descriptor)
    {
        $this->descriptor = $descriptor;

        return $this;
    }

    /**
     * @return string
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

    public function isReferenced() {
        return $this->adTags != null && $this->adTags->count() > 0;
    }

    /**
     * @return PersistentCollection
     */
    public function getLibSlotTags()
    {
        if (null === $this->libSlotTags) {
            $this->libSlotTags = new ArrayCollection();
        }

        return $this->libSlotTags;
    }

    /**
     * @inheritdoc
     */
    public function setLibSlotTags($libSlotTags)
    {
        $this->libSlotTags = $libSlotTags;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAssociatedTagCount()
    {
        return count($this->getAdTags());
    }


    function __toString()
    {
        return $this->id . $this->getName();
    }

    /**
     * @inheritdoc
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @inheritdoc
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @inheritdoc
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getVastTags()
    {
        return $this->vastTags;
    }

    /**
     * @inheritdoc
     */
    public function setVastTags($vastTags)
    {
        $this->vastTags = $vastTags;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPlayerWidth()
    {
        return $this->playerWidth;
    }

    /**
     * @inheritdoc
     */
    public function setPlayerWidth($playerWidth)
    {
        $this->playerWidth = $playerWidth;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPlayerHeight()
    {
        return $this->playerHeight;
    }

    /**
     * @inheritdoc
     */
    public function setPlayerHeight($playerHeight)
    {
        $this->playerHeight = $playerHeight;
        return $this;
    }
}