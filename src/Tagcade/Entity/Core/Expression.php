<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\Expression as ExpressionModel;

class Expression extends ExpressionModel
{
    protected $id;

    protected $expressionDescriptor;
    protected $startingPosition;
    protected $expressionInJs;

    protected $dynamicAdSlot;
    protected $expectAdSlot;

    public function __construct()
    {}
}