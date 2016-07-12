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

    /**
     * @param $fieldValues
     * @return Expression
     */
    public static function createExpressionFromArray($fieldValues)
    {
        $expression = new self();
        foreach ($fieldValues as $field=>$value) {
            $method = sprintf('set%s',ucwords($field));
            $expression->$method($value);
        }
        return $expression;
    }
}