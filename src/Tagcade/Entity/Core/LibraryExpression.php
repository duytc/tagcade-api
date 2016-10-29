<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\LibraryExpression as LibraryExpressionModel;

class LibraryExpression extends LibraryExpressionModel {

    protected $id;
    protected $name;
    protected $expressionDescriptor;
    protected $startingPosition = 1;
    protected $expressions;
    protected $libraryDynamicAdSlot;
    protected $expectLibraryAdSlot;

    function __construct()
    {
    }

    /**
     * @param array $fieldValues
     * @return LibraryExpression
     */
    public static function createLibraryExpression(array $fieldValues)
    {
        $libraryExpression = new self();

        foreach ($fieldValues as $field=>$value) {
            $method = sprintf('set%s', ucwords($field));
            $libraryExpression->$method($value);
        }
        return $libraryExpression;
    }
}