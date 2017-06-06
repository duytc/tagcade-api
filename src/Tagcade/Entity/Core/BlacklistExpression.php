<?php


namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\BlacklistExpression as BlacklistExpressionModel;

class BlacklistExpression extends BlacklistExpressionModel
{
    protected $id;
    protected $blacklist;
    protected $libraryAdTag;
    protected $libraryExpression;
}