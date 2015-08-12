<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\ModelInterface;

interface ExpressionInterface extends ModelInterface
{

    /**
     * @return mixed
     */
    public function getExpressionDescriptor();
    /**
     * @param mixed $expressionDescriptor
     */
    public function setExpressionDescriptor($expressionDescriptor);

    /**
     * @return int
     */
    public function getStartingPosition();

    /**
     * @param int $startingPosition
     */
    public function setStartingPosition($startingPosition);

    /**
     * @return BaseAdSlotInterface
     */
    public function getExpectAdSlot();
    /**
     * @param ReportableAdSlotInterface $expectAdSlot
     */
    public function setExpectAdSlot(ReportableAdSlotInterface $expectAdSlot);

    /**
     * @return mixed
     */
    public function getExpressionInJs();
    /**
     * @param mixed $expressionInJs
     */
    public function setExpressionInJs($expressionInJs);

    /**
     * @return BaseAdSlotInterface
     */
    public function getDefaultAdSlot();


    /**
     * @param LibraryExpressionInterface $libraryExpression
     * @return $this
     */
    public function setLibraryExpression($libraryExpression);

    /**
     * @return LibraryExpressionInterface
     */
    public function getLibraryExpression();

    /**
     * @return DynamicAdSlotInterface
     */
    public function getDynamicAdSlot();

    /**
     * @param DynamicAdSlotInterface $dynamicAdSlot
     */
    public function setDynamicAdSlot($dynamicAdSlot);

    public function getDeletedAt();


}