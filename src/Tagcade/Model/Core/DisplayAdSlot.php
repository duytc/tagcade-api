<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\AdSlot;

class DisplayAdSlot extends AdSlotAbstract implements DisplayAdSlotInterface, ReportableAdSlotInterface
{
    protected $id;
    /**
     * @var SiteInterface
     */
    protected $site;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setSlotType(AdSlotAbstract::TYPE_DISPLAY);
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
     * Calculate CheckSum string of an given AdSlot
     * by concatenating major properties together with null value ignored, then returning the MD5 hash
     * @return string
     */
    public function checkSum()
    {
        $array = array(
            $this->getType(),
            $this->getLibraryAdSlot()->getId()
        );

        $adTags = $this->getAdTags()->toArray();

        usort($adTags, function(AdTagInterface $a, AdTagInterface $b) {
            return strcmp($a->getRefId(), $b->getRefId());
        });

        /** @var AdTagInterface $t */
        foreach($adTags as $t){
            $array[] =  $t->checkSum();
        }

        return md5(serialize($array));
    }
}