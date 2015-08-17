<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Tagcade\Model\ModelInterface;

interface BaseLibraryAdSlotInterface extends ModelInterface
{
    /**
     * @param mixed $id
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
     * @return mixed
     */
    public function setVisible($visible);

    /**
     * @return mixed
     */
    public function isVisible();

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
     * @param LibrarySlotTagInterface $libSlotTags
     */
    public function setLibSlotTags($libSlotTags);

    /**
     * @return int|null
     */
    public function getPublisherId();

    /**
     * return int
     */
    public function getAssociatedSlotCount();
}