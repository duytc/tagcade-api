<?php

namespace Tagcade\Model\Core;


use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\ModelInterface;

interface LibraryExpressionInterface extends ModelInterface
{
    /**
     * @param $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return self
     */
    public function setName($name);

    /**
     * @return array
     */
    public function getExpressionDescriptor();

    /**
     * @param string $expressionDescriptor
     * @return $this
     */
    public function setExpressionDescriptor($expressionDescriptor);

    /**
     * @return int
     */
    public function getStartingPosition();

    /**
     * @param int $startingPosition
     * @return $this
     */
    public function setStartingPosition($startingPosition);

    /**
     * @return ArrayCollection
     */
    public function getExpressions();

    /**
     * @param ExpressionInterface[] $expressions
     * @return $this
     */
    public function setExpressions($expressions);

    /**
     * @return LibraryDynamicAdSlotInterface
     */
    public function getLibraryDynamicAdSlot();

    /**
     * @param LibraryDynamicAdSlotInterface $libraryDynamicAdSlot
     * @return $this
     */
    public function setLibraryDynamicAdSlot($libraryDynamicAdSlot);

    /**
     * @return BaseLibraryAdSlotInterface
     */
    public function getExpectLibraryAdSlot();

    /**
     * @param BaseLibraryAdSlotInterface $expectLibraryAdSlot
     * @return $this
     */
    public function setExpectLibraryAdSlot($expectLibraryAdSlot);

    /**
     * @return array
     */
    public function getExpressionInJs();

}