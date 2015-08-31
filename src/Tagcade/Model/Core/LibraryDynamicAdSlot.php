<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\LibraryAdSlotAbstract;
use Tagcade\Exception\RuntimeException;

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
     * @inheritdoc
     */
    public function setNative($native)
    {
        $this->native = $native;

        return $this;
    }

    /**
     * @return LibraryExpressionInterface[]
     */
    public function getLibraryExpressions()
    {
        return $this->libraryExpressions;
    }

    /**
     * @inheritdoc
     */
    public function setLibraryExpressions($libraryExpressions)
    {
        $this->libraryExpressions = $libraryExpressions;

        return $this;
    }

    /**
     * @return BaseLibraryAdSlotInterface
     */
    public function getDefaultLibraryAdSlot()
    {
        return $this->defaultLibraryAdSlot;
    }

    /**
     * @inheritdoc
     */
    public function setDefaultLibraryAdSlot($defaultLibraryAdSlot)
    {
        $this->defaultLibraryAdSlot = $defaultLibraryAdSlot;

        return $this;
    }

    /**
     * @return string
     */
    public function checkSum()
    {
        throw new RuntimeException('not support checksum for dynamic ad slot');
    }


    public function __toString()
    {
        return $this->id . $this->getName();
    }
}