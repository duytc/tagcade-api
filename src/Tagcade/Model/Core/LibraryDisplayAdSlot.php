<?php

namespace Tagcade\Model\Core;

use Tagcade\Entity\Core\LibraryAdSlotAbstract;
use Tagcade\Model\User\Role\PublisherInterface;

class LibraryDisplayAdSlot extends LibraryAdSlotAbstract implements LibraryDisplayAdSlotInterface, ReportableLibraryAdSlotInterface
{
    protected $id;
    protected $width;
    protected $height;
    protected $autoFit = false;
    protected $passbackMode;
    /**
     * @var PublisherInterface
     */
    protected $publisher;

    /**
     * @param string $name
     * @param int $width
     * @param int $height
     */
    public function __construct($name, $width, $height)
    {
        parent::__construct($name);

        $this->width = $width;
        $this->height = $height;
        $this->autoFit = false;
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
     * @return DisplayAdSlotInterface[]
     */
    public function getDisplayAdSlots()
    {
        return $this->adSlots;
    }


    /**
     * @return PublisherInterface
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @param PublisherInterface $publisher
     * @return $this
     */
    public function setPublisher(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
        return $this;
    }

    /**
     * Calculate CheckSum string of an given library AdSlot
     * by concatenating major properties together with null value ignored, then returning the MD5 hash
     * @return string
     */
    public function checkSum()
    {
        $array = array(
            parent::TYPE_DISPLAY,
            $this->getId()
        );

        $adTags = $this->getLibSlotTags()->toArray();

        usort($adTags, function(LibrarySlotTagInterface $a, LibrarySlotTagInterface $b) {
            return strcmp($a->getRefId(), $b->getRefId());
        });

        /** @var LibrarySlotTagInterface $t */
        foreach($adTags as $t){
            $array[] =  $t->checkSum();
        }

        return md5(serialize($array));
    }

    /**
     * @return boolean
     */
    public function isAutoFit()
    {
        return $this->autoFit;
    }

    /**
     * @param boolean $autoFit
     * @return $this
     */
    public function setAutoFit($autoFit)
    {
        $this->autoFit = $autoFit;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassbackMode()
    {
        return $this->passbackMode;
    }

    /**
     * @param string $passbackMode
     * @return self
     */
    public function setPassbackMode($passbackMode)
    {
        $this->passbackMode = $passbackMode;
        return $this;
    }



    public function __toString()
    {
        return $this->id . $this->getName();
    }
}