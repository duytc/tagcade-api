<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Exception\RuntimeException;
use Tagcade\Form\Type\ExpressionFormType;
use Tagcade\Model\Core\ExpressionInterface;

class UpdateExpressionInJsListener {

    static $INTERNAL_VARIABLE_MAP = ['${PAGEURL}'=>'location.href'];
    /**
     * handle event preUpdate one expression, this auto update expressionInJS field.
     * @param PreUpdateEventArgs $args
     */
//    public function preUpdate($args)
//    {
//        $entity = $args->getObject();
//        if (!$entity instanceof ExpressionInterface ) {
//            return;
//        }
//
//        if ($args->hasChangedField('expectAdSlot')) {
//            $this->updateExpectAdSlotForExpressionInJs($entity);
//        }
//
//        if ($args->hasChangedField('expressionDescriptor')) {
//            $this->updateExpressionForExpressionInJs($entity);
//        }
//
//    }

    /**
     * handle event prePersist one expression, this auto update expressionInJS field.
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof ExpressionInterface) {
            return;
        }

        $this->createExpressionInJs($entity);
    }

//    /**
//     * update ExpressionInJS, built from expressionDescriptor automatically
//     * @param ExpressionInterface $expression
//     */
//    private function updateExpectAdSlotForExpressionInJs(ExpressionInterface $expression)
//    {
//        $expressionInJS = $expression->getExpressionInJS();
//        if (null == $expressionInJS || count($expressionInJS) < 1 || !array_key_exists('expectedAdSlot', $expressionInJS)) {
//            return;
//        }
//
//        $expressionInJS['expectedAdSlot'] = $expression->getExpectAdSlot()->getId();
//
//        $expression->setExpressionInJs($expressionInJS);
//    }

//    private function updateExpressionForExpressionInJs(ExpressionInterface $expression)
//    {
//        $expressionDescriptor = $expression->getExpressionDescriptor();
//        if (null == $expressionDescriptor || count($expressionDescriptor) < 1) {
//            return;
//        }
//
//        $expressionInJS = $expression->getExpressionInJS();
//        $convertedExpression = $this->simplifyExpression($expressionDescriptor);
//
//        $expressionInJS['expression'] = $convertedExpression['expression'];
//        $expressionInJS['vars'] = $convertedExpression['vars'];
//        $expression->setExpressionInJs($expressionInJS);
//    }

    protected function createExpressionInJs(ExpressionInterface $expression)
    {
        $expressionDescriptor = $expression->getExpressionDescriptor();
        if (null == $expressionDescriptor || count($expressionDescriptor) < 1) {
            return;
        }

        $convertedExpression = $this->simplifyExpression($expressionDescriptor);

        if (null !== $convertedExpression) {
            $expression->setExpressionInJs(
                [
                    'vars'=> $convertedExpression['vars'],
                    'expectedAdSlot'=>$expression->getExpectAdSlot()->getId(),
                    'expression' => $convertedExpression['expression'],
                    'startingPosition'=>$expression->getStartingPosition(),
                ]
            );
        }
    }

    /**
     * simplify Expression, make array of pair {expression, expectedAdSlot}
     * @param array $expression
     * @return array('vars'=>[ [name:'', type: '], [name:'', type: ']] , 'expression'=>'')
     */
    public function simplifyExpression(array $expression)
    {
        return $this->createExpressionObject($expression);
    }

    /**
     * create Expression String as '( A && (B || C) && D ...)'
     * @param array $expression
     * @return string
     */
    protected function createExpressionObject(array $expression)
    {
        //try to get groupType, if not null => is group, else is not group
        $groupType = (isset($expression[ExpressionFormType::KEY_GROUP_TYPE])) ? ExpressionFormType::$VAL_GROUPS[$expression[ExpressionFormType::KEY_GROUP_TYPE]] : null;

        return ($groupType != null)
            ? $this->createExpressionAsGroupObject($groupType, $expression[ExpressionFormType::KEY_GROUP_VAL])
            : $this->createExpressionAsConditionObject($expression);
    }

    /**
     * create Group (And/Or/...) Expression String as A &&/||/... B
     * @param $operator
     * @param array $expressionAsGroup
     * @return string
     */
    protected function createExpressionAsGroupObject($operator, array $expressionAsGroup)
    {
        //not really needed? already verified before in formType?
        if ($expressionAsGroup == null || count($expressionAsGroup) < 1) {
            throw new RuntimeException('expect at least on expression');
        }

        if (count($expressionAsGroup) == 1) { // condition object
            return $this->createExpressionAsConditionObject($expressionAsGroup[0]);
        }

        $vars = [];

        $expString = '(' . implode(
            $this->getOperatorInJS($operator),
            array_map(
                function (array $expElement) use (&$vars){

                    $exp = $this->createExpressionObject($expElement);
                    array_push($vars, $exp['vars']);

                    return $exp['expression'];
                },
                $expressionAsGroup
            )
        )
        . ')';

        // filter unique
        $vars = array_unique($vars, SORT_REGULAR);

        return ['vars'=>$vars, 'expression'=>$expString];


    }

    /**
     * create Other Expression String as condition as 'a > 1', 'b != 2', ...
     * @param array $expression
     * @return array('vars'=>, 'expression'=>)
     */
    protected function createExpressionAsConditionObject(array $expression)
    {
        $type = array_key_exists(ExpressionFormType::KEY_EXPRESSION_TYPE, $expression) ? $expression[ExpressionFormType::KEY_EXPRESSION_TYPE] : "";
        $val = $expression[ExpressionFormType::KEY_EXPRESSION_VAL];

        if (null !== $type) {
            $type = strtolower($type);
        }

        switch ($type) {
            case 'string' :
                $val = '"' . $val . '"';
                break;
        }

        $exp = $this->getConditionInJS($expression[ExpressionFormType::KEY_EXPRESSION_VAR], $expression[ExpressionFormType::KEY_EXPRESSION_CMP], $val);

        return ['vars'=>['name'=>$expression[ExpressionFormType::KEY_EXPRESSION_VAR], 'type'=>$type], 'expression'=>$exp];
    }

    /**
     * get Operator In JS. Return operator mapped from UI to Javascript syntax. e.g: 'AND' => '&&'
     * @param $operator
     * @return string
     */
    protected function getOperatorInJS($operator)
    {
        if (array_key_exists($operator, ExpressionFormType::$GROUP_TYPE_MAP_JS)) {
            return ExpressionFormType::$GROUP_TYPE_MAP_JS[$operator];
        }

        return $operator;
    }

    /**
     * get condition In JS. Return condition mapped from UI to Javascript syntax. e.g:
     * + 'a, >, 1' => 'a>1',
     * + 'b, length >=, 10' => 'b.length >= 10'
     * @param $var
     * @param $cmp
     * @param $val
     * @return null|string return null if cmp not supported
     */
    private function getConditionInJS($var, $cmp, $val)
    {
        $var = trim($var); // make sure not spacing

        //if MATH => format as '$var . $cmp . $val'
        if (in_array($cmp, ExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_MATH)) {
            return $this->getConditionInJSForMath($var, $cmp, $val);
        }

        //if STRING => format as 'func($var) . $real-cmp . $val', where func = $cmp['func'], $real-cmp = $cmp['cmp']
        if (array_key_exists($cmp, ExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_STRING)) {
            return $this->getConditionInJSForString($var, $cmp, $val);
        }

        return null;
    }

    /**
     * get condition In JS for MATH. Return condition mapped from UI to Javascript syntax. e.g:
     * + 'a, >, 1' => 'a>1'
     * @param $var
     * @param $cmp
     * @param $val
     * @return string
     */
    private function getConditionInJSForMath($var, $cmp, $val)
    {
        $var = $this->getConvertedVar($var);

        return '(window.' . $var . $cmp . $val . ')';
    }

    private function getConvertedVar($var)
    {
        // Convert local variable to js variable
        if (isset(self::$INTERNAL_VARIABLE_MAP[$var]) && !empty(self::$INTERNAL_VARIABLE_MAP[$var])) {
            $var = self::$INTERNAL_VARIABLE_MAP[$var];
        }

        return $var;
    }

    /**
     * get condition In JS for MATH. Return condition mapped from UI to Javascript syntax. e.g:
     * + 'b, length >=, 10' => 'b.length >= 10'
     * @param $var
     * @param $cmp
     * @param $val
     * @return null|string return null if not supported
     */
    private function getConditionInJSForString($var, $cmp, $val)
    {
        // Convert local variable to js variable
        $var = $this->getConvertedVar($var);

        //return '$var.length . $real-cmp . $val'; e.g: 'a.length > 1'
        if (strpos($cmp, 'length') !== false) { //do not use '!strpos()'
            return '(window.' .
            $var . '.' . ExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . ExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['cmp'] . $val .
            ')';
        }

        // Below functions use regex, hence we have to remove the quotes from json
        $val = str_replace('"','', $val);

        if ($cmp === 'contains') {
            return '(window.' .
            $var . '.' . ExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $val . '/i) > -1' .
            ')';
        }

        if ($cmp === 'notContains') {
            return '(window.' .
            $var . '.' . ExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $val . '/i) < 0' .
            ')';
        }

        if ($cmp === 'startsWith') {
            return '(window.' .
            $var . '.' . ExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $val . '/i) === 0' .
            ')';
        }

        if ($cmp === 'notStartsWith') {
            return '(window.' .
            $var . '.' . ExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $val . '/i) != 0' .
            ')';
        }

        if ($cmp === 'endsWith') {

            return '(window.' .
            $var . '.' . ExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $val . '$/i) === (window.' . $var . '.length - "' . $val . '".length)' .
            ')';
        }

        if ($cmp === 'notEndsWith') {

            return '(window.' .
            $var . '.' . ExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $val . '$/i) != (window.' . $var . '.length - "' . $val . '".length)' .
            ')';
        }

        return null;
    }
} 