<?php

namespace Tagcade\Model\Core;

use Doctrine\ORM\PersistentCollection;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface BaseLibraryAdSlotInterface extends ModelInterface
{
    /**
     * @param int $id
     * @return self
     */
    public function setId($id);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string $name
     * @return self
     */
    public function setName($name);

    /**
     * @param $visible
     * @return self
     */
    public function setVisible($visible);

    /**
     * @return mixed
     */
    public function isVisible();

    /**
     * @return bool
     */
    public function isBelongedToRonAdSlot();

    /**
     * @return mixed
     */
    public function getLibType();

    /**
     * @return PersistentCollection
     */
    public function getAdSlots();

    /**
     * @return PersistentCollection
     */
    public function getLibSlotTags();

    /**
     * @param LibrarySlotTagInterface $libSlotTag
     * @return $this
     */
    public function addLibSlotTag(LibrarySlotTagInterface $libSlotTag);

    /**
     * @param LibrarySlotTagInterface $libSlotTag
     * @return self
     */
    public function removeLibSlotTag(LibrarySlotTagInterface $libSlotTag);

    /**
     * @param array $libSlotTags
     * @return self
     */
    public function setLibSlotTags($libSlotTags);

    /**
     * @return int|null
     */
    public function getPublisherId();

    /**
     * @return PublisherInterface|null
     */
    public function getPublisher();

    /**
     * return int
     */
    public function getAssociatedSlotCount();

    /**
     * @return RonAdSlotInterface
     */
    public function getRonAdSlot();

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @return self
     */
    public function setRonAdSlot($ronAdSlot);

    /**
     * @return string
     */
    public function checkSum();
}