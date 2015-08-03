<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Core\SiteInterface;

class Expression implements ExpressionInterface
{
    protected $id;

    protected $expressionDescriptor;
    protected $startingPosition;
    protected $deletedAt;
    /**
     * @var LibraryDynamicAdSlotInterface
     */
    protected $libraryDynamicAdSlot;
    /**
     * @var BaseAdSlotInterface
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
     * @return int
     */
    public function getStartingPosition()
    {
        return $this->startingPosition;
    }

    /**
     * @param int $startingPosition
     */
    public function setStartingPosition($startingPosition)
    {
        $this->startingPosition = $startingPosition;
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
     * @return BaseAdSlotInterface
     */
    public function getExpectAdSlot()
    {
        return $this->expectAdSlot;
    }

    /**
     * @param ReportableAdSlotInterface $expectAdSlot
     */
    public function setExpectAdSlot(ReportableAdSlotInterface $expectAdSlot)
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
        return $this->id;
    }
}