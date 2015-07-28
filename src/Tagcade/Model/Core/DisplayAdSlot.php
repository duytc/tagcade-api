<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\AdSlotAbstract;

class DisplayAdSlot extends AdSlotAbstract implements DisplayAdSlotInterface, ReportableAdSlotInterface
{
    protected $id;
    protected $name;
    /**
     * @var SiteInterface
     */
    protected $site;

    /**
     * @var LibraryDisplayAdSlotInterface $libraryDisplayAdSlot
     */
    protected $libraryDisplayAdSlot;

    /**
     * @var LibraryDynamicAdSlotInterface[]
     */
    protected $defaultLibraryDynamicAdSlots;
    
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function getWidth()
    {
        if($this->libraryDisplayAdSlot == null) return null;

        return $this->libraryDisplayAdSlot->getWidth();
    }

    /**
     * @inheritdoc
     */
    public function setWidth($width)
    {
        if($this->libraryDisplayAdSlot){
            $this->libraryDisplayAdSlot->setWidth($width);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHeight()
    {
        if($this->libraryDisplayAdSlot == null) return null;

        return $this->libraryDisplayAdSlot->getHeight();
    }

    /**
     * @inheritdoc
     */
    public function setHeight($height)
    {
        if($this->libraryDisplayAdSlot){
            $this->libraryDisplayAdSlot->setHeight($height);
        }

        return $this;
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
        return $this->name;
    }


    /**
     * @inheritdoc
     */
    public function getCoReferencedAdSlots()
    {
        return $this->libraryDisplayAdSlot->getDisplayAdSlots();
    }

    /**
     * @return LibraryDisplayAdSlotInterface
     */
    public function getLibraryDisplayAdSlot()
    {
        return $this->libraryDisplayAdSlot;
    }

    /**
     * @return LibraryDisplayAdSlotInterface
     */
    public function getLibraryAdSlot()
    {
        return $this->libraryDisplayAdSlot;
    }


    public function setLibraryAdSlot($libraryAdSlot)
    {
        $this->libraryDisplayAdSlot = $libraryAdSlot;
    }

    /**
     * @param LibraryDisplayAdSlotInterface $libraryDisplayAdSlot
     * @return mixed
     */
    public function setLibraryDisplayAdSlot(LibraryDisplayAdSlotInterface $libraryDisplayAdSlot)
    {
        $this->libraryDisplayAdSlot = $libraryDisplayAdSlot;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Calculate CheckSum string of an given AdSlot
     * by concatenating major properties together with null value ignored, then returning the MD5 hash
     * @return string
     */
    public function checkSum()
    {
        $array = array(
            $this->getSite()->getId(),
            $this->getType(),
            $this->getLibraryDisplayAdSlot()->getId()
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