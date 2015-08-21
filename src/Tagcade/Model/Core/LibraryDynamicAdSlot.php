<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\LibraryAdSlotAbstract;

class LibraryDynamicAdSlot extends LibraryAdSlotAbstract implements LibraryDynamicAdSlotInterface
{
    protected $id;

    /**
     * @var LibraryExpressionInterface[]
     */
    protected $libraryExpressions;

    /**
     * @var BaseLibraryAdSlotInterface
     */
    protected $defaultLibraryAdSlot;

    /** @var $native */
    protected $native;
    protected $deletedAt;

    public function __construct()
    {
        $this->libraryExpressions = new ArrayCollection();
    }


    /**
     * @return boolean
     */
    public function isSupportedNative()
    {
        return $this->native;
    }

    /**
     * @return mixed
     */
    public function getNative()
    {
        return $this->native;
    }

    /**
     * @param mixed $native
     */
    public function setNative($native)
    {
        $this->native = $native;
    }

    /**
     * @return LibraryExpressionInterface[]
     */
    public function getLibraryExpressions()
    {
        return $this->libraryExpressions;
    }

    /**
     * @param LibraryExpressionInterface[] $libraryExpressions
     */
    public function setLibraryExpressions($libraryExpressions)
    {
        $this->libraryExpressions = $libraryExpressions;
    }

    /**
     * @return BaseLibraryAdSlotInterface
     */
    public function getDefaultLibraryAdSlot()
    {
        return $this->defaultLibraryAdSlot;
    }

    /**
     * @param BaseLibraryAdSlotInterface $defaultLibraryAdSlot
     */
    public function setDefaultLibraryAdSlot($defaultLibraryAdSlot)
    {
        $this->defaultLibraryAdSlot = $defaultLibraryAdSlot;
    }


    public function __toString()
    {
        return $this->id . $this->getName();
    }
}