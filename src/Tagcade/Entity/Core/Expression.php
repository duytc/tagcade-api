<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\Expression as ExpressionModel;

class Expression extends ExpressionModel
{
    protected $id;
    protected $expectAdSlot;
    protected $defaultAdSlot;
    protected $expressionInJs;
    protected $libraryExpression;
    protected $dynamicAdSlot;
    protected $hbBidPrice;

    public function __construct()
    {}
}