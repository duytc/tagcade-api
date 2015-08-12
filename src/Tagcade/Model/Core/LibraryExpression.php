<?php

namespace Tagcade\Model\Core;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

class LibraryExpression implements LibraryExpressionInterface{

    protected $id;
    /**
     * @var string
     */
    protected $expressionDescriptor;

    /**
     * @var int
     */
    protected $startingPosition = 1;

    /**
     * @var ExpressionInterface[]
     */
    protected $expressions;

    /**
     * @var LibraryDynamicAdSlotInterface
     */
    protected $libraryDynamicAdSlot;

    /**
     * @var BaseLibraryAdSlotInterface
     */
    protected $expectLibraryAdSlot;


    protected $deletedAt;


    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpressionDescriptor()
    {
        return $this->expressionDescriptor;
    }

    /**
     * @param string $expressionDescriptor
     * @return $this
     */
    public function setExpressionDescriptor($expressionDescriptor)
    {
        $this->expressionDescriptor = $expressionDescriptor;

        return $this;
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
     * @return $this
     */
    public function setStartingPosition($startingPosition)
    {
        $this->startingPosition = $startingPosition;

        return $this;
    }

    /**
     * @return ExpressionInterface[]
     */
    public function getExpressions()
    {
        if($this->expressions === null) $this->expressions = new ArrayCollection();

        return $this->expressions;
    }

    /**
     * @param ExpressionInterface $expressions
     * @return $this
     */
    public function setExpressions($expressions)
    {
//        if(!empty($expressions) && $expressions === $this->expressions) {
//            reset($expressions);
//            $expressions[key($expressions)] = clone current($expressions);
//        }

        $this->expressions = $expressions;

        return $this;
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
     * @return $this
     */
    public function setLibraryDynamicAdSlot($libraryDynamicAdSlot)
    {
        $this->libraryDynamicAdSlot = $libraryDynamicAdSlot;

        return $this;
    }

    /**
     * @return BaseLibraryAdSlotInterface
     */
    public function getExpectLibraryAdSlot()
    {
        return $this->expectLibraryAdSlot;
    }

    /**
     * @param BaseLibraryAdSlotInterface $expectLibraryAdSlot
     * @return $this
     */
    public function setExpectLibraryAdSlot($expectLibraryAdSlot)
    {
        $this->expectLibraryAdSlot = $expectLibraryAdSlot;

        return $this;
    }

    function __toString()
    {
        return $this->id . "";
    }


}