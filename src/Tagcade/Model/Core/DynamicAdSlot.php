<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\SiteInterface;

class DynamicAdSlot extends AdSlotAbstract implements DynamicAdSlotInterface
{
    protected $id;
    protected $name;
    /**
     * @var BaseAdSlotInterface
     */
    protected $defaultAdSlot;

    /**
     * @var LibraryDynamicAdSlotInterface $libraryDynamicAdSlot
     */
    protected $libraryDynamicAdSlot;

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
        if($this->getLibraryDynamicAdSlot() == null) return null;

        return $this->getLibraryDynamicAdSlot()->getDefaultAdSlot();
    }

    /**
     * @param BaseAdSlotInterface $defaultAdSlot
     */
    public function setDefaultAdSlot($defaultAdSlot)
    {
        if($this->getLibraryDynamicAdSlot() != null)
        {
            $this->getLibraryDynamicAdSlot()->setDefaultAdSlot($defaultAdSlot);
        }
    }

    /**
     * @return ExpressionInterface[]
     */
    public function getExpressions()
    {
        if ($this->getLibraryDynamicAdSlot() == null) return [];

        return $this->getLibraryDynamicAdSlot()->getExpressions();
    }

    /**
     * @param ExpressionInterface[] $expressions
     */
    public function setExpressions($expressions)
    {
        if($this->getLibraryDynamicAdSlot() != null)
        {
            $this->getLibraryDynamicAdSlot()->setExpressions($expressions);
        }
    }

    /**
     * @return boolean
     */
    public function isSupportedNative()
    {
        if($this->getLibraryDynamicAdSlot() == null) return false;

        return $this->getLibraryDynamicAdSlot()->isSupportedNative();
    }

    /**
     * @return mixed
     */
    public function getNative()
    {
        if($this->getLibraryDynamicAdSlot() == null) return false;

        return $this->getLibraryDynamicAdSlot()->isSupportedNative();
    }

    /**
     * @param mixed $native
     */
    public function setNative($native)
    {
        if($this->getLibraryDynamicAdSlot() != null)
        {
            $this->getLibraryDynamicAdSlot()->setNative($native);
        }
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
        return $this->name;
    }

    /**
     * @return LibraryDynamicAdSlotInterface
     */
    public function getLibraryDynamicAdSlot()
    {
        return $this->libraryDynamicAdSlot;
    }

    /**
     * @param LibraryDynamicAdSlotInterface $libraryDynamicAdSlot
     */
    public function setLibraryDynamicAdSlot($libraryDynamicAdSlot)
    {
        $this->libraryDynamicAdSlot = $libraryDynamicAdSlot;
    }


    /**
     * @return LibraryDynamicAdSlotInterface
     */
    public function getLibraryAdSlot()
    {
        return $this->libraryDynamicAdSlot;
    }

    public function setLibraryAdSlot($libaryAdSlot)
    {
        $this->libraryDynamicAdSlot = $libaryAdSlot;
        return $this;
    }


    /**
     * get the list of DynamicAdSlot that also refers to the DynamicAdSlotLib of this entity
     * @return DynamicAdSlotInterface[]
     */
    public function getCoReferencedAdSlots()
    {
        return $this->libraryDynamicAdSlot->getDynamicAdSlots();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return $this|\Tagcade\Model\Core\BaseAdSlotInterface|void
     */
    public function setName($name)
    {
        $this->name = $name;

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