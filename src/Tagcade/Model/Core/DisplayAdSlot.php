<?php

namespace Tagcade\Model\Core;

use Tagcade\Entity\Core\AdSlotAbstract;

class DisplayAdSlot extends AdSlotAbstract implements DisplayAdSlotInterface, ReportableAdSlotInterface
{
    const RTB_STATUS_DEFAULT = self::RTB_DISABLED;

    protected $id;
    /**
     * @var SiteInterface
     */
    protected $site;
    protected $rtbStatus;
    protected $floorPrice;
    protected $hbBidPrice;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->rtbStatus = self::RTB_STATUS_DEFAULT;
        $this->setSlotType(AdSlotAbstract::TYPE_DISPLAY);
    }

    /**
     * @inheritdoc
     */
    public function getHbBidPrice()
    {
        return $this->hbBidPrice;
    }

    /**
     * @inheritdoc
     */
    public function setHbBidPrice($hbBidPrice)
    {
        $this->hbBidPrice = $hbBidPrice;
        return $this;
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
     * @inheritdoc
     */
    public function isAutoFit()
    {
        if($this->libraryAdSlot instanceof LibraryDisplayAdSlotInterface){
            return $this->libraryAdSlot->isAutoFit();
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function setAutoFit($autoFit)
    {
        if($this->libraryAdSlot instanceof LibraryDisplayAdSlotInterface){
            $this->libraryAdSlot->setAutoFit($autoFit);
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
     * @return string
     */
    public function getPassbackMode()
    {
        if ($this->libraryAdSlot instanceof LibraryDisplayAdSlotInterface) {
            return $this->libraryAdSlot->getPassbackMode();
        }

        return null;
    }

    /**
     * @param string $passbackMode
     * @return self
     */
    public function setPassbackMode($passbackMode)
    {
        if ($this->libraryAdSlot instanceof LibraryDisplayAdSlotInterface) {
            $this->libraryAdSlot->setPassbackMode($passbackMode);
        }

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

    /**
     * @return float
     */
    public function getFloorPrice()
    {
        return $this->floorPrice;
    }

    /**
     * @param float $floorPrice
     */
    public function setFloorPrice($floorPrice)
    {
        $this->floorPrice = $floorPrice;
    }

    public function removeFloorPrice()
    {
        $this->floorPrice = null;
    }

    public function isRTBEnabled()
    {
        if (!$this->getSite()->getPublisher()->hasRtbModule()) {
            return false;
        }

        if ($this->rtbStatus === self::RTB_INHERITED) {
            return $this->getSite()->isRTBEnabled();
        }

        return $this->rtbStatus === self::RTB_ENABLED;
    }

    /**
     * @return integer
     */
    public function getRtbStatus()
    {
        return $this->rtbStatus;
    }

    /**
     * @param integer $rtbStatus
     * @return self
     */
    public function setRtbStatus($rtbStatus)
    {
        $this->rtbStatus = null === $rtbStatus ? self::RTB_STATUS_DEFAULT : $rtbStatus;
        return $this;
    }

    public function __toString()
    {
        return $this->id . $this->getName();
    }
}