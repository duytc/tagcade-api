<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Model\Core\SiteInterface;

class DynamicAdSlot extends AdSlotAbstract implements DynamicAdSlotInterface
{
    protected $id;

    protected $name;

    /**
     * @var BaseAdSlotInterface
     */
    protected $defaultAdSlot;

    protected $deletedAt;
    /**
     * @var ExpressionInterface[]
     */
    protected $expressions;

    /** @var $native */
    protected $native;

    public function __construct()
    {
        $this->expressions = new ArrayCollection();
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

    /**
     * @return ExpressionInterface[]
     */
    public function getExpressions()
    {
        if ($this->expressions == null) {
            $this->expressions = new ArrayCollection();
        }

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

    /**
     * @return mixed
     */
    public function getType()
    {
        return self::TYPE_DYNAMIC;
    }


    public function __toString()
    {
        return parent::__toString();
    }
}