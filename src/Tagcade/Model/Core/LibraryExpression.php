<?php

namespace Tagcade\Model\Core;


use Doctrine\Common\Collections\ArrayCollection;

class LibraryExpression implements LibraryExpressionInterface, ExpressionJsProducibleInterface
{
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /** @var array */
    protected $expressionDescriptor;

    /** @var int */
    protected $startingPosition = 1;

    /** @var ExpressionInterface[] */
    protected $expressions;

    /** @var LibraryDynamicAdSlotInterface */
    protected $libraryDynamicAdSlot;

    /** @var BaseLibraryAdSlotInterface */
    protected $expectLibraryAdSlot;

    protected $deletedAt;

    protected $expressionInJs;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getExpressionDescriptor()
    {
        return $this->expressionDescriptor;
    }

    /**
     * @return array
     */
    public function getDescriptor()
    {
        return $this->getExpressionDescriptor();
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
        if ($this->expressions === null) $this->expressions = new ArrayCollection();

        return $this->expressions;
    }

    /**
     * @param ExpressionInterface[] $expressions
     * @return $this
     */
    public function setExpressions($expressions)
    {
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

    function __toString()
    {
        return $this->id . "";
    }
}