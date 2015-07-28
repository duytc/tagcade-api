<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\ModelInterface;
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
     * @return mixed
     */
    public function setVisible($visible);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     */
    public function setId($id);

    /**
     * @return PublisherInterface
     */
    public function getPublisher();

    /**
     * @param PublisherInterface $publisher
     */
    public function setPublisher(PublisherInterface $publisher);

    /**
     * @return int|null
     */
    public function getPublisherId();

    /**
     * @return NativeAdSlotInterface[]
     */
    public function getNativeAdSlots();

    /**
     * @param NativeAdSlotInterface[] $nativeAdSlots
     */
    public function setNativeAdSlots($nativeAdSlots);
}