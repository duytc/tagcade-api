<?php

namespace Tagcade\Entity\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
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

    /** @var ArrayCollection */
    protected $ronAdSlot;
    /**
     * @var LibrarySlotTagInterface[]
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


    /**
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @return bool
     */
    public function isBelongedToRonAdSlot()
    {
        return $this->getRonAdSlot() instanceof RonAdSlotInterface;
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
     * @return self
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
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
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
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
     * @return self
     */
    public function setPublisher(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPublisherId()
    {
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
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
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
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getLibSlotTags()
    {
        if (null === $this->libSlotTags) {
            $this->libSlotTags = new ArrayCollection();
        }

        return $this->libSlotTags;
    }

    /**
     * @param LibrarySlotTagInterface $libSlotTag
     * @return $this
     */
    public function addLibSlotTag(LibrarySlotTagInterface $libSlotTag)
    {
        $this->getLibSlotTags()->add($libSlotTag);

        return $this;
    }

    public function removeLibSlotTag(LibrarySlotTagInterface $libSlotTag)
    {
        $this->getLibSlotTags()->removeElement($libSlotTag);
        return $this;
    }

    public function isRonAdSlot()
    {
        if ($this->getRonAdSlot() instanceof RonAdSlotInterface) {
            return array(
                'id' => $this->getRonAdSlot()->getId()
            );
        }

        return array(
            'id' => null
        );
    }
    /**
     * @param LibrarySlotTagInterface $libSlotTags
     * @return self
     */
    public function setLibSlotTags($libSlotTags)
    {
        $this->libSlotTags = $libSlotTags;
        return $this;
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
     * @return self
     */
    public function setAdSlots($adSlots)
    {
        $this->adSlots = $adSlots;
        return $this;
    }

    /**
     * return int
     */
    public function getAssociatedSlotCount()
    {
        return count($this->getAdSlots());
    }

    /**
     * @return RonAdSlotInterface
     */
    public function getRonAdSlot()
    {
        if ($this->ronAdSlot === null) {
            $this->ronAdSlot = new ArrayCollection();
        }

        if (count($this->ronAdSlot->toArray()) > 0) {
            return $this->ronAdSlot->current();
        }

        return null;
    }

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @return self
     */
    public function setRonAdSlot($ronAdSlot)
    {
        if ($ronAdSlot === null) {
            $this->ronAdSlot = new ArrayCollection();
        }

        if ($ronAdSlot instanceof RonAdSlotInterface) {
            $this->ronAdSlot->clear();
            $this->ronAdSlot->add($ronAdSlot);
        }

        return $this;
    }

    function __toString()
    {
        return $this->id . $this->getName();
    }
}