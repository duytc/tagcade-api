<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Model\Core\SiteInterface;

class NativeAdSlot extends AdSlotAbstract implements NativeAdSlotInterface, ReportableAdSlotInterface
{
    protected $id;

    protected $name;

    /**
     * @var DynamicAdSlotInterface[]
     */
    protected $defaultDynamicAdSlots;
    /**
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct();

        $this->name = $name;
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
        return self::TYPE_NATIVE;
    }


    public function __toString()
    {
        return parent::__toString();
    }
}