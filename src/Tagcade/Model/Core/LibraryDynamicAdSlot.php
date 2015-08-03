<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\LibraryAdSlotAbstract;

class LibraryDynamicAdSlot extends LibraryAdSlotAbstract implements LibraryDynamicAdSlotInterface
{
    protected $id;

    protected $deletedAt;
    /**
     * @var ExpressionInterface[]
     */
    protected $expressions;

    /**
     * @var BaseAdSlotInterface
     */
    protected $defaultAdSlot;

    /** @var $native */
    protected $native;

    public function __construct()
    {
        $this->expressions = new ArrayCollection();
    }


    /**
     * @return ExpressionInterface[]
     */
    public function getExpressions()
    {
        return $this->expressions;
    }

    /**
     * @param ExpressionInterface[] $expressions
     */
    public function setExpressions($expressions)
    {
        $this->expressions = $expressions;
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


    public function __toString()
    {
        return $this->id . $this->getName();
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
     */
    public function setDefaultAdSlot($defaultAdSlot)
    {
        $this->defaultAdSlot = $defaultAdSlot;
    }
}