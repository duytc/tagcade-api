<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\LibraryExpression as LibraryExpressionModel;

class LibraryExpression extends LibraryExpressionModel {

    protected $id;
    protected $expressionDescriptor;
    protected $startingPosition = 1;
    protected $expressions;
    protected $libraryDynamicAdSlot;
    protected $expectLibraryAdSlot;

    function __construct()
    {
    }
}