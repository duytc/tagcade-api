<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\SiteInterface;

class DynamicAdSlot extends AdSlotAbstract implements DynamicAdSlotInterface
{
    protected $id;
    /**
     * @var BaseAdSlotInterface
     */
    protected $defaultAdSlot;

    protected $deletedAt;


    public function __construct()
    {
        $this->expressions = new ArrayCollection();
    }

    /**
     * @return BaseAdSlotInterface
     */
    public function getDefaultAdSlot()
    {
        if($this->getLibraryAdSlot() == null) return null;

        return $this->getLibraryAdSlot()->getDefaultAdSlot();
    }

    /**
     * @param BaseAdSlotInterface $defaultAdSlot
     */
    public function setDefaultAdSlot($defaultAdSlot)
    {
        if($this->getLibraryAdSlot() != null)
        {
            $this->getLibraryAdSlot()->setDefaultAdSlot($defaultAdSlot);
        }
    }

    /**
     * @return ExpressionInterface[]
     */
    public function getExpressions()
    {
        if ($this->getLibraryAdSlot() == null) return new ArrayCollection();

        return $this->getLibraryAdSlot()->getExpressions();
    }

    public function setExpressions($expressions)
    {
        if($this->getLibraryAdSlot() != null)
        {
            $this->getLibraryAdSlot()->setExpressions($expressions);
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSupportedNative()
    {
        if($this->getLibraryAdSlot() == null) return false;

        return $this->getLibraryAdSlot()->isSupportedNative();
    }

    /**
     * @return mixed
     */
    public function getNative()
    {
        if($this->getLibraryAdSlot() == null) return false;

        return $this->getLibraryAdSlot()->isSupportedNative();
    }

    public function setNative($native)
    {
        if($this->getLibraryAdSlot() != null)
        {
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
    public function setLibraryAdSlot(BaseLibraryAdSlotInterface $libraryAdSlot)
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