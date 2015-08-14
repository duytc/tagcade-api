<?php

namespace Tagcade\Entity\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\User\Role\PublisherInterface;

abstract class LibraryAdSlotAbstract
{
    const TYPE_DISPLAY = 'display';
    const TYPE_NATIVE = 'native';
    const TYPE_DYNAMIC = 'dynamic';

    protected $id;
    protected $name;
    protected $visible = false;
    protected $libType;
    protected $type; // a hack to have type in output json
    /**
     * @var LibrarySlotTagInterface
     */
    protected $libSlotTags;

    /**
     * @var BaseAdSlotInterface[]
     */
    protected $adSlots;
    /**
     * @var PublisherInterface
     */
    protected $publisher;

    protected $deletedAt;

    function __construct($name)
    {
        $this->name = $name;
    }

    function __toString()
    {
        return $this->id . $this->getName();
    }

    /**
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @return boolean
     */
    public function getVisible()
    {
        return $this->visible;
    }
    /**
     * @param boolean $visible
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     */
    public function setPublisher(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * @return int|null
     */
    public function getPublisherId(){
        return $this->publisher->getId();
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
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }


    public function getLibType()
    {
        if ($this instanceof LibraryDisplayAdSlotInterface) {
            return self::TYPE_DISPLAY;
        }
        else if ($this instanceof LibraryNativeAdSlotInterface) {
            return self::TYPE_NATIVE;
        }
        else if ($this instanceof LibraryDynamicAdSlotInterface) {
            return self::TYPE_DYNAMIC;

        }

        return 'unknown';
    }
    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return LibrarySlotTagInterface
     */
    public function getLibSlotTags()
    {
        if (null === $this->libSlotTags) {
            $this->libSlotTags = new ArrayCollection();
        }

        return $this->libSlotTags;
    }

    /**
     * @param LibrarySlotTagInterface $libSlotTags
     */
    public function setLibSlotTags($libSlotTags)
    {
        $this->libSlotTags = $libSlotTags;
    }

    /**
     * @return \Tagcade\Model\Core\BaseAdSlotInterface[]
     */
    public function getAdSlots()
    {
        if (null === $this->adSlots) {
            $this->adSlots = new ArrayCollection();
        }
        
        return $this->adSlots;
    }

    /**
     * @param \Tagcade\Model\Core\BaseAdSlotInterface[] $adSlots
     */
    public function setAdSlots($adSlots)
    {
        $this->adSlots = $adSlots;
    }

    /**
     * return int
     */
    public function getAssociatedSlotCount() {
        return count($this->getAdSlots());
    }
    
}