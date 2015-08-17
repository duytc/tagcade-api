<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\Expression as ExpressionModel;
use Tagcade\Model\Core\LibraryExpressionInterface;

class Expression extends ExpressionModel
{
    protected $id;
    /**
     * @var BaseAdSlotInterface
     */
    protected $expectAdSlot;

    /**
     * @var BaseAdSlotInterface
     */
    protected $defaultAdSlot;

    protected $expressionInJs;

    /**
     * @var LibraryExpressionInterface
     */
    protected $libraryExpression;

    /**
     * @var DynamicAdSlotInterface
     */
    protected $dynamicAdSlot;

    public function __construct()
    {}
}