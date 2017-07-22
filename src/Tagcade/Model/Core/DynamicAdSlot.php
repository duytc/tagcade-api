<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Exception\LogicException;

class DynamicAdSlot extends AdSlotAbstract implements DynamicAdSlotInterface
{
    protected $id;

    /** @var ExpressionInterface[] */
    protected $expressions;

    /** @var BaseAdSlotInterface */
    protected $defaultAdSlot;

    protected $deletedAt;

    public function __construct()
    {
        parent::__construct();

        $this->expressions = new ArrayCollection();
        $this->setSlotType(AdSlotAbstract::TYPE_NATIVE);
    }

    /**
     * @return BaseAdSlotInterface
     */
    public function getDefaultAdSlot()
    {
        return $this->defaultAdSlot;
    }

    /**
     * @param BaseAdSlotInterface $defaultAdSlot
     * @return $this
     */
    public function setDefaultAdSlot($defaultAdSlot)
    {
        $this->defaultAdSlot = $defaultAdSlot;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getExpressions()
    {
        if ($this->expressions === null) {
            $this->expressions = new ArrayCollection();
        }

        return $this->expressions;
    }

    public function setExpressions($expressions)
    {
        $this->expressions = $expressions;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSupportedNative()
    {
        if ($this->getLibraryAdSlot() == null) return false;

        return $this->getLibraryAdSlot()->isSupportedNative();
    }

    /**
     * @return mixed
     */
    public function getNative()
    {
        if ($this->getLibraryAdSlot() == null) return false;

        return $this->getLibraryAdSlot()->isSupportedNative();
    }

    public function setNative($native)
    {
        if ($this->getLibraryAdSlot() != null) {
            $this->getLibraryAdSlot()->setNative($native);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return self::TYPE_DYNAMIC;
    }


    public function __toString()
    {
        return $this->id . $this->getName();
    }

    /**
     * @return LibraryDynamicAdSlotInterface
     */
    public function getLibraryAdSlot()
    {
        return $this->libraryAdSlot;
    }

    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @return $this
     */
    public function setLibraryAdSlot($libraryAdSlot)
    {
        $this->libraryAdSlot = $libraryAdSlot;

        return $this;
    }

    /**
     * @return string
     */
    public function checkSum()
    {
        throw new LogicException('Checksum is not supported with DynamicAdSlot');
    }
}