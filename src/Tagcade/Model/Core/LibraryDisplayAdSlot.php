<?php

namespace Tagcade\Model\Core;

use Tagcade\Entity\Core\LibraryAdSlotAbstract;
use Tagcade\Model\User\Role\PublisherInterface;

class LibraryDisplayAdSlot extends LibraryAdSlotAbstract implements LibraryDisplayAdSlotInterface
{
    protected $id;
    protected $width;
    protected $height;
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
        return $this->id . $this->getName();
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
}