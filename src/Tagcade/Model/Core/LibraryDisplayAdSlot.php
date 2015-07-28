<?php

namespace Tagcade\Model\Core;

use Tagcade\Entity\Core\LibraryAdSlotAbstract;
use Tagcade\Model\User\Role\PublisherInterface;

class LibraryDisplayAdSlot extends LibraryAdSlotAbstract implements LibraryDisplayAdSlotInterface
{
    protected $id;
    protected $referenceName;
    protected $width;
    protected $height;
    /**
     * @var PublisherInterface
     */
    protected $publisher;

    /**
     * @var DisplayAdSlotInterface[]
     */
    protected $displayAdSlots;
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


    public function __toString()
    {
        return parent::__toString();
    }

    /**
     * @return DisplayAdSlotInterface[]
     */
    public function getDisplayAdSlots()
    {
        return $this->displayAdSlots;
    }

    /**
     * @param DisplayAdSlotInterface[] $displayAdSlots
     */
    public function setDisplayAdSlots($displayAdSlots)
    {
        $this->displayAdSlots = $displayAdSlots;
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
     * @return mixed
     */
    public function getReferenceName()
    {
        return $this->referenceName;
    }

    /**
     * @param mixed $referenceName
     */
    public function setReferenceName($referenceName)
    {
        $this->referenceName = $referenceName;
    }

    public function isReferenced() {
        return $this->displayAdSlots != null && $this->displayAdSlots->count() > 0;
    }

    public function getLibType()
    {
        return self::TYPE_DISPLAY;
    }

    public function getAdSlots()
    {
        return $this->displayAdSlots;
    }


}