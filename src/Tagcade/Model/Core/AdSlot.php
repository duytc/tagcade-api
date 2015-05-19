<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Model\Core\SiteInterface;

class AdSlot extends AdSlotAbstract implements AdSlotInterface
{
    protected $id;

    /**
     * @var SiteInterface
     */
    protected $site;
    protected $name;
    protected $width;
    protected $height;
    protected $adTags;

    /**
     * @var DynamicAdSlotInterface[]
     */
    protected $defaultDynamicAdSlots;
    /**
     * @param string $name
     * @param int $width
     * @param int $height
     */
    public function __construct($name, $width, $height)
    {
        $this->name = $name;
        $this->width = $width;
        $this->height = $height;
        $this->adTags = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function getSite()
    {
        return $this->site;
    }

    public function setSite(SiteInterface $site)
    {
        $this->site = $site;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSiteId()
    {
        if (!$this->site) {
            return null;
        }

        return $this->site->getId();
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
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @inheritdoc
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @inheritdoc
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
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

    /**
     * @param ArrayCollection $adTags
     */
    public function setAdTags($adTags)
    {
        $this->adTags = $adTags;
    }

    /**
     * @return DynamicAdSlotInterface[]
     */
    public function defaultDynamicAdSlots()
    {
        return $this->defaultDynamicAdSlots;
    }

    public function __toString()
    {
        return $this->name;
    }
}