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
    const AD_TYPE_DYNAMIC = 3;

    protected $id;

    protected $html;

    protected $visible = false;

    /** int - type of AdTags*/
    protected $adType = 0;

    /** array - json_array, descriptor of WaterfallTag*/
    protected $descriptor;

    /** array - json_array*/
    protected $inBannerDescriptor;

    /** @var AdNetworkInterface */
    protected $adNetwork;

    /** @var Collection */
    protected $adTags;

    /** @var LibrarySlotTagInterface */
    protected $libSlotTags;

    protected $name;
    /**
     * @var array
     */
    protected $expressionDescriptor;

    /** @var float */
    protected $sellPrice;

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
     * @inheritdoc
     */
    public function getAdTags()
    {
        if (null === $this->adTags) {
            $this->adTags = new ArrayCollection();
        }

        return $this->adTags;
    }

    public function addAdTag(AdTagInterface $adTag)
    {
        if (null === $this->adTags) {
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
     * get inBannerDescriptor as json_array
     * @return array
     */
    public function getInBannerDescriptor()
    {
        return $this->inBannerDescriptor;
    }

    /**
     * @inheritdoc
     */
    public function setInBannerDescriptor($inBannerDescriptor)
    {
        $this->inBannerDescriptor = $inBannerDescriptor;

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

    public function isReferenced()
    {
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
        return $this->adTags->count();
    }

    function __toString()
    {
        return $this->id . $this->getName();
    }

    /**
     * @return array
     */
    public function getExpressionDescriptor()
    {
        return $this->expressionDescriptor;
    }

    /**
     * @param array $expressionDescriptor
     */
    public function setExpressionDescriptor($expressionDescriptor)
    {
        $this->expressionDescriptor = $expressionDescriptor;
    }

    /**
     * @inheritdoc
     */
    public function getSellPrice()
    {
        return $this->sellPrice;
    }

    /**
     * @inheritdoc
     */
    public function setSellPrice($sellPrice)
    {
        $this->sellPrice = $sellPrice;

        return $this;
    }
}