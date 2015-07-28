<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryDisplayAdSlotInterface extends BaseLibraryAdSlotInterface
{
    /**
     * @return int|null
     */
    public function getWidth();

    /**
     * @param int $width
     * @return self
     */
    public function setWidth($width);

    /**
     * @return int|null
     */
    public function getHeight();

    /**
     * @param int $height
     * @return self
     */
    public function setHeight($height);

    /**
     * @return PublisherInterface
     */
    public function getPublisher();

    /**
     * @param PublisherInterface $publisher
     * @return mixed
     */
    public function setPublisher(PublisherInterface $publisher);


    /**
     * @return mixed
     */
    public function getPublisherId();


    /**
     * @return DisplayAdSlotInterface[]
     */
    public function getDisplayAdSlots();

    /**
     * @param DisplayAdSlotInterface[] $displayAdSlots
     */
    public function setDisplayAdSlots($displayAdSlots);

}