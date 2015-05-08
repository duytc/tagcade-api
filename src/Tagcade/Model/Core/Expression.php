<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Core\SiteInterface;

class Expression implements ExpressionInterface
{
    protected $id;

    protected $expressionDescriptor;
    protected $deletedAt;
    /**
     * @var DynamicAdSlotInterface
     */
    protected $dynamicAdSlot;
    /**
     * @var AdSlotInterface
     */
    protected $expectAdSlot;

    protected $expressionInJs;

    public function __construct()
    {

    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getExpressionDescriptor()
    {
        return $this->expressionDescriptor;
    }

    /**
     * @param mixed $expressionDescriptor
     */
    public function setExpressionDescriptor($expressionDescriptor)
    {
        $this->expressionDescriptor = $expressionDescriptor;
    }

    /**
     * @return DynamicAdSlotInterface
     */
    public function getDynamicAdSlot()
    {
        return $this->dynamicAdSlot;
    }

    /**
     * @param DynamicAdSlotInterface $dynamicAdSlot
     */
    public function setDynamicAdSlot($dynamicAdSlot)
    {
        $this->dynamicAdSlot = $dynamicAdSlot;
    }

    /**
     * @return AdSlotInterface
     */
    public function getExpectAdSlot()
    {
        return $this->expectAdSlot;
    }

    /**
     * @param AdSlotInterface $expectAdSlot
     */
    public function setExpectAdSlot(AdSlotInterface $expectAdSlot)
    {
        $this->expectAdSlot = $expectAdSlot;
    }

    /**
     * @return mixed
     */
    public function getExpressionInJs()
    {
        return $this->expressionInJs;
    }

    /**
     * @param mixed $expressionInJs
     */
    public function setExpressionInJs($expressionInJs)
    {
        $this->expressionInJs = $expressionInJs;
    }



    public function __toString()
    {
        return "test";
    }
}