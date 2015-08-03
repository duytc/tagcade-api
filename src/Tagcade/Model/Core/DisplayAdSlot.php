<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\AdSlotAbstract;

class DisplayAdSlot extends AdSlotAbstract implements DisplayAdSlotInterface, ReportableAdSlotInterface
{
    protected $id;
    /**
     * @var SiteInterface
     */
    protected $site;

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
        if($this->libraryAdSlot instanceof LibraryDisplayAdSlotInterface)
        {
            return $this->libraryAdSlot->getWidth();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function setWidth($width)
    {
        if($this->libraryAdSlot instanceof LibraryDisplayAdSlotInterface){
            $this->libraryAdSlot->setWidth($width);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHeight()
    {
        if($this->libraryAdSlot instanceof LibraryDisplayAdSlotInterface){
            return $this->libraryAdSlot->getHeight();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function setHeight($height)
    {
        if($this->libraryAdSlot instanceof LibraryDisplayAdSlotInterface){
            $this->libraryAdSlot->setHeight($height);
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
        return $this->id . $this->getName();
    }

    /**
     * @return LibraryDisplayAdSlotInterface
     */
    public function getLibraryDisplayAdSlot()
    {
        return $this->libraryAdSlot;
    }

    /**
     * @param LibraryDisplayAdSlotInterface $libraryDisplayAdSlot
     * @return $this
     */
    public function setLibraryDisplayAdSlot(LibraryDisplayAdSlotInterface $libraryDisplayAdSlot)
    {
        $this->libraryAdSlot = $libraryDisplayAdSlot;

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
            $temp = $libraryDynamicAdSlot->getAdSlots();
            if($temp->count() < 1) continue; // ignore library with no slot referencing

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

    public function setDefaultLibraryDynamicAdSlots($defaultLibraryDynamicAdSlots)
    {
        $this->defaultLibraryDynamicAdSlots = $defaultLibraryDynamicAdSlots;

        return $this;
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
            $this->getLibraryAdSlot()->getId()
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