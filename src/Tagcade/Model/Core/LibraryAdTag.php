<?php

namespace Tagcade\Model\Core;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

class LibraryAdTag implements LibraryAdTagInterface{

    protected $id;

    protected $html;

    protected $visible = false;
    /** int - type of AdTags*/
    protected $adType = 0;
    /** array - json_array, descriptor of AdTag*/
    protected $descriptor;
    /**
     * @var AdNetworkInterface
     */
    protected $adNetwork;

    protected $adTags;
    /**
     * @var LibrarySlotTagInterface
     */
    protected $libSlotTags;

    protected $name;
    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @inheritdoc
     */
    public function setHtml($html)
    {
        $this->html = $html;
    }


    /**
     * @inheritdoc
     */
    public function setAdNetwork($adNetwork)
    {
        $this->adNetwork = $adNetwork;
    }

    /**
     * @inheritdoc
     */
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @inheritdoc
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }


    /**
     * @inheritdoc
     */
    public function getVisible()
    {
        return $this->visible;
    }

    function __toString()
    {
        return $this->id . $this->getName();
    }

    /**
     * @inheritdoc
     */
    public function getAdTags()
    {
        if(null === $this->adTags) {
            $this->adTags = new ArrayCollection();
        }

        return $this->adTags;
    }

    /**
     * This indicate ad tag type: image, custom, etc..
     * get AdType
     * @return int
     */
    public function getAdType()
    {
        return $this->adType;
    }

    /**
     * set AdType
     * @param int $adType
     */
    public function setAdType($adType)
    {
        $this->adType = $adType;
    }

    /**
     * get Descriptor as json_array
     * @return array
     */
    public function getDescriptor()
    {
        return $this->descriptor;
    }

    /**
     * set Descriptor formatted as json_array
     * @param array $descriptor
     */
    public function setDescriptor($descriptor)
    {
        $this->descriptor = $descriptor;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function isReferenced() {
        return $this->adTags != null && $this->adTags->count() > 0;
    }

    /**
     * @return PersistentCollection
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
}