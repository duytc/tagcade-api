<?php

namespace Tagcade\Bundle\ApiBundle\Service;


use Tagcade\Exception\InvalidFormException;
use Tagcade\Model\Core\ExpressionJsProducibleInterface;

interface ExpressionInJsGeneratorInterface {

    /**
     * Generate simple array containing keys that make js evaluation easier
     * ['vars'=>[{name: 'name1', type: 'string'}], expression: 'js expression']
     *
     * @param ExpressionJsProducibleInterface $expression
     * @return array
     */
    public function generateExpressionInJs(ExpressionJsProducibleInterface $expression);

    /**
     * validate Expression which contains GROUP_AND / GROUP_OR and/or {'var', 'cmp', val}
     * @param mixed|array $expression
     * @throws InvalidFormException if has one
     * - 'var', 'cmp', 'val' keys not set
     */
    public function validateExpressionDescriptor(array $expression);


} 