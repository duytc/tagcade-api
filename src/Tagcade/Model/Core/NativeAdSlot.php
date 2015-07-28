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
     * @var LibraryNativeAdSlotInterface $libraryNativeAdSlot
     */
    protected $libraryNativeAdSlot;
    /**
     * @var LibraryDynamicAdSlotInterface[]
     */
    protected $defaultLibraryDynamicAdSlots;
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
     * @return mixed
     */
    public function getType()
    {
        return self::TYPE_NATIVE;
    }


    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return LibraryNativeAdSlotInterface
     */
    public function getLibraryAdSlot()
    {
        return $this->libraryNativeAdSlot;
    }

    /**
     * @return LibraryNativeAdSlotInterface
     */
    public function getLibraryNativeAdSlot()
    {
        return $this->libraryNativeAdSlot;
    }

    /**
     * @param LibraryNativeAdSlotInterface $libraryNativeAdSlot
     */
    public function setLibraryNativeAdSlot($libraryNativeAdSlot)
    {
        $this->libraryNativeAdSlot = $libraryNativeAdSlot;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCoReferencedAdSlots()
    {
        return $this->libraryNativeAdSlot->getNativeAdSlots();
    }

    /**
     * @return LibraryDynamicAdSlotInterface[]
     */
    public function getDefaultLibraryDynamicAdSlots()
    {
        return $this->defaultLibraryDynamicAdSlots;
    }

    /**
     * @param LibraryDynamicAdSlotInterface[] $defaultLibraryDynamicAdSlots
     */
    public function setDefaultLibraryDynamicAdSlots($defaultLibraryDynamicAdSlots)
    {
        $this->defaultLibraryDynamicAdSlots = $defaultLibraryDynamicAdSlots;
    }

    /**
     * @return DynamicAdSlotInterface[]
     */
    public function defaultDynamicAdSlots()
    {
        $dynamicAdSlots = [];

        $libraryDynamicAdSlots = $this->getDefaultLibraryDynamicAdSlots();

        if(null == $libraryDynamicAdSlots) return $dynamicAdSlots;

        foreach($libraryDynamicAdSlots as $libraryDynamicAdSlot){
            $temp = $libraryDynamicAdSlot->getDynamicAdSlots();
            if($temp->count() < 1) continue;

            $dynamicAdSlots = array_merge($dynamicAdSlots, $temp->toArray());
        }

        return array_unique($dynamicAdSlots);
    }

    public function setLibraryAdSlot($libraryAdSlot)
    {
        $this->libraryNativeAdSlot = $libraryAdSlot;
        return $this;
    }

    /**
     * @return string
     */
    public function checkSum()
    {
        $array = array(
            $this->getSite()->getId(),
            $this->getType(),
            $this->getLibraryNativeAdSlot()->getId()
        );

        $adTags = $this->getAdTags()->toArray();

        usort($adTags, function(AdTagInterface $a, AdTagInterface $b) {
            return ($a->getId() < $b->getId()) ? -1 : 1;
        });

        /** @var AdTagInterface $t */
        foreach($adTags as $t){
            $array[] =  $t->checkSum();
        }

        return md5(serialize($array));
    }
}