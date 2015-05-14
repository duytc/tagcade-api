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
     * @return DynamicAdSlotInterface
     */
    public function getDynamicAdSlot();

    /**
     * @param DynamicAdSlotInterface $dynamicAdSlot
     */
    public function setDynamicAdSlot($dynamicAdSlot);
    /**
     * @return AdSlotInterface
     */
    public function getExpectAdSlot();
    /**
     * @param AdSlotInterface $expectAdSlot
     */
    public function setExpectAdSlot(AdSlotInterface $expectAdSlot);

    /**
     * @return mixed
     */
    public function getExpressionInJs();
    /**
     * @param mixed $expressionInJs
     */
    public function setExpressionInJs($expressionInJs);

}