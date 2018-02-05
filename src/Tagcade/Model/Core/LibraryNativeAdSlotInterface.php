<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryNativeAdSlotInterface extends BaseLibraryAdSlotInterface
{
    /**
     * @return ArrayCollection
     */
    public function defaultDynamicAdSlots();

    /**
     * @return mixed
     */
    public function isVisible();

    /**
     * @param $visible
     * @return self
     */
    public function setVisible($visible);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id);

    /**
     * @return PublisherInterface
     */
    public function getPublisher();

    /**
     * @param PublisherInterface $publisher
     * @return self
     */
    public function setPublisher(PublisherInterface $publisher);

    /**
     * @return int|null
     */
    public function getPublisherId();

    /**
     * @return float
     */
    public function getBuyPrice();

    /**
     * @param float $buyPrice
     * @return self
     */
    public function setBuyPrice($buyPrice);
}