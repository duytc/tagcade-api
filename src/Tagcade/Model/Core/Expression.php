<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Core\SiteInterface;

class Expression implements ExpressionInterface
{
    protected $id;
    /**
     * @var BaseAdSlotInterface
     */
    protected $expectAdSlot;

    protected $expressionInJs;

    /**
     * @var LibraryExpressionInterface
     */
    protected $libraryExpression;

    /**
     * @var DynamicAdSlotInterface
     */
    protected $dynamicAdSlot;

    protected $deletedAt;

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
        if($this->libraryExpression === null) return null;

        return $this->libraryExpression->getExpressionDescriptor();
    }

    /**
     * @param mixed $expressionDescriptor
     * @return $this
     */
    public function setExpressionDescriptor($expressionDescriptor)
    {
        if($this->libraryExpression instanceof LibraryExpressionInterface) {
            $this->libraryExpression->setExpressionDescriptor($expressionDescriptor);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getStartingPosition()
    {
        if($this->libraryExpression === null) return null;

        return $this->libraryExpression->getStartingPosition();
    }

    /**
     * @param int $startingPosition
     * @return $this
     */
    public function setStartingPosition($startingPosition)
    {
        if($this->libraryExpression instanceof LibraryExpressionInterface) {
            $this->libraryExpression->setStartingPosition($startingPosition);
        }

        return $this;
    }


    /**
     * @return BaseAdSlotInterface
     */
    public function getExpectAdSlot()
    {
        return $this->expectAdSlot;
    }

    /**
     * @param ReportableAdSlotInterface $expectAdSlot
     * @return $this
     */
    public function setExpectAdSlot(ReportableAdSlotInterface $expectAdSlot)
    {
        $this->expectAdSlot = $expectAdSlot;

        return $this;
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
     * @return $this
     */
    public function setExpressionInJs($expressionInJs)
    {
        $this->expressionInJs = $expressionInJs;

        return $this;
    }



    /**
     * @return BaseAdSlotInterface
     */
    public function getDefaultAdSlot()
    {
        if($this->getDynamicAdSlot() instanceof DynamicAdSlotInterface) {
            return $this->getDynamicAdSlot()->getDefaultAdSlot();
        }
    }


    /**
     * @param LibraryExpressionInterface $libraryExpression
     * @return $this
     */
    public function setLibraryExpression($libraryExpression)
    {
        $this->libraryExpression = $libraryExpression;
        return $this;
    }

    /**
     * @return LibraryExpressionInterface
     */
    public function getLibraryExpression()
    {
        return $this->libraryExpression;
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
     * @return $this
     */
    public function setDynamicAdSlot($dynamicAdSlot)
    {
        $this->dynamicAdSlot = $dynamicAdSlot;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }



    public function __toString()
    {
        return (string)$this->id;
    }
}
