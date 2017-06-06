<?php


namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\WhiteListExpression as WhiteListExpressionModel;
class WhiteListExpression extends WhiteListExpressionModel
{
    protected $id;
    protected $whiteList;
    protected $libraryAdTag;
    protected $libraryExpression;
}