<?php

namespace Tagcade\Entity\Core;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryExpression as LibraryExpressionModel;

class LibraryExpression extends LibraryExpressionModel {

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
     * @var ExpressionInterface
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

    function __construct()
    {
    }
}