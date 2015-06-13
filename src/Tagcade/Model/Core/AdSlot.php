<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Model\Core\SiteInterface;

class AdSlot extends AdSlotAbstract implements AdSlotInterface, ReportableAdSlotInterface
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

    /**
     * @return mixed
     */
    public function getType()
    {
        return self::TYPE_DISPLAY;
    }

    public function __toString()
    {
        return parent::__toString();
    }
}