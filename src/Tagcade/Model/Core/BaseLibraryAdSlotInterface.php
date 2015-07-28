<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
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
    public function getReferenceName();

    /**
     * @param string $referenceName
     * @return self
     */
    public function setReferenceName($referenceName);

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

    public function getAdSlots();

}