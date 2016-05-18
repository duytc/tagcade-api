<?php

namespace Tagcade\Bundle\ApiBundle\Service;


use Tagcade\Model\Core\ExpressionJsProducibleInterface;

interface ExpressionInJsGeneratorInterface
{
    /**
     * Generate simple array containing keys that make js evaluation easier
     * ['vars'=>[{name: 'name1', type: 'string'}], expression: 'js expression']
     *
     * @param ExpressionJsProducibleInterface $expression
     * @return array
     */
    public function generateExpressionInJs(ExpressionJsProducibleInterface $expression);
} 