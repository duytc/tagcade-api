<?php

namespace Tagcade\Model\Core;

use DateTime;
use Tagcade\Model\ModelInterface;

interface ExpressionInterface extends ModelInterface
{
    const VARS = 'vars';
    const EXPRESSION = 'expression';
    const DOMAIN_CHECKS = 'domainChecks';
    const TARGETING = 'targeting';

    /**
     * @return mixed
     */
    public function getExpressionDescriptor();

    /**
     * @param mixed $expressionDescriptor
     * @return self
     */
    public function setExpressionDescriptor($expressionDescriptor);

    /**
     * @return int
     */
    public function getStartingPosition();

    /**
     * @param int $startingPosition
     * @return self
     */
    public function setStartingPosition($startingPosition);

    /**
     * @return BaseAdSlotInterface
     */
    public function getExpectAdSlot();

    /**
     * @param ReportableAdSlotInterface $expectAdSlot
     * @return self
     */
    public function setExpectAdSlot(ReportableAdSlotInterface $expectAdSlot);

    /**
     * @return mixed
     */
    public function getExpressionInJs();

    /**
     * @param mixed $expressionInJs
     * @return self
     */
    public function setExpressionInJs($expressionInJs);

    /**
     * @return BaseAdSlotInterface
     */
    public function getDefaultAdSlot();

    /**
     * @param LibraryExpressionInterface $libraryExpression
     * @return self
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
     * @return self
     */
    public function setDynamicAdSlot($dynamicAdSlot);

    /**
     * @return  DateTime
     */
    public function getDeletedAt();

    /**
     * @return mixed
     */
    public function getHbBidPrice();

    /**
     * @param mixed $hbBidPrice
     * @return self
     */
    public function setHbBidPrice($hbBidPrice);
}