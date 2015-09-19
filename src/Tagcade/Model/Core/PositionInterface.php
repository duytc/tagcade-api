<?php

namespace Tagcade\Model\Core;


use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\ModelInterface;

interface PositionInterface extends ModelInterface {
    /**
     * @return int|null
     */
    public function getPosition();

    /**
     * @param int $position
     * @return self
     */
    public function setPosition($position);

    public function getDeletedAt();

    /**
     * Get then entity that contain current object
     * @return mixed
     */
    public function getContainer();

    /**
     * @return mixed
     */
    public function getClassName();

    /**
     * Get those entities that belong to the same container with the current entity
     * @return mixed
     */
    public function getSiblings();

    /**
     * @return string
     */
    public function getRefId();

    /**
     * @param string $refId
     * @return self
     */
    public function setRefId($refId);
}