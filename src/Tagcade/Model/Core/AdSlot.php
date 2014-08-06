<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;

class AdSlot implements AdSlotInterface
{
    protected $id;
    protected $site;
    protected $name;
    protected $width;
    protected $height;
    protected $adTags;

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
        return $this->adTags;
    }
}