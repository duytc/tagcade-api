<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Exception\RuntimeException;
use Tagcade\Form\Type\ExpressionFormType;
use Tagcade\Form\Type\LibraryExpressionFormType;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Entity\Core\Expression;
use Tagcade\Model\Core\LibraryExpressionInterface;

class UpdateExpressionInJsListener {

    static $INTERNAL_VARIABLE_MAP = ['${PAGEURL}'=>'location.href', '${USERAGENT}'=>'navigator.userAgent'];

    protected $updatedExpressions = [];

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

    /**
     * handle event preUpdate one expression, this auto update expressionInJS field.
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof LibraryExpressionInterface && ($args->hasChangedField('expressionDescriptor') || $args->hasChangedField('startingPosition'))) {
            $expressions = $entity->getExpressions();
            foreach ($expressions as $exp) {
                $this->createExpressionInJs($exp);
                $this->updatedExpressions[] = $exp;
            }
        }
        else if($entity instanceof ExpressionInterface) {
            $this->createExpressionInJs($entity);
        }
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if(!empty($this->updatedExpressions)) {
            $em = $args->getEntityManager();
            foreach ($this->updatedExpressions as $exp) {
                $em->merge($exp);
            }

            $this->updatedExpressions = []; // reset updated expressions

            $em->flush();
        }
    }

    protected function createExpressionInJs(ExpressionInterface $expression)
    {
        $expressionDescriptor = $expression->getExpressionDescriptor();
        if (null == $expressionDescriptor || count($expressionDescriptor) < 1) {
            return;
        }

        $convertedExpression = $this->simplifyExpression($expressionDescriptor);

        if (null !== $convertedExpression) {

            $expInJs = [
                'vars'=> $convertedExpression['vars'],
                'expectedAdSlot'=>$expression->getExpectAdSlot()->getId(),
                'expression' => $convertedExpression['expression']
            ];

            if (is_int($expression->getStartingPosition())) {
                $expInJs['startingPosition'] = $expression->getStartingPosition();
            }

            $expression->setExpressionInJs($expInJs);
        }
    }

    /**
     * simplify Expression, converting from descriptor structure with groupType, etc. into object containing string expression and all available variables
     *
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
        $groupType = (isset($expression[LibraryExpressionFormType::KEY_GROUP_TYPE])) ? LibraryExpressionFormType::$VAL_GROUPS[$expression[LibraryExpressionFormType::KEY_GROUP_TYPE]] : null;

        return ($groupType != null)
            ? $this->createExpressionAsGroupObject($groupType, $expression[LibraryExpressionFormType::KEY_GROUP_VAL])
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
            $expression = $expressionAsGroup[0];
            return $this->createExpressionObject($expression);
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
        $type = array_key_exists(LibraryExpressionFormType::KEY_EXPRESSION_TYPE, $expression) ? $expression[LibraryExpressionFormType::KEY_EXPRESSION_TYPE] : "";
        $val = $expression[LibraryExpressionFormType::KEY_EXPRESSION_VAL];

        if (null !== $type) {
            $type = strtolower($type);
        }

        switch ($type) {
            case 'string' :
                $val = '"' . $val . '"';
                break;
        }

        $exp = $this->getConditionInJS($expression[LibraryExpressionFormType::KEY_EXPRESSION_VAR], $expression[LibraryExpressionFormType::KEY_EXPRESSION_CMP], $val);

        return ['vars'=>['name'=>$expression[LibraryExpressionFormType::KEY_EXPRESSION_VAR], 'type'=>$type], 'expression'=>$exp];
    }

    /**
     * get Operator In JS. Return operator mapped from UI to Javascript syntax. e.g: 'AND' => '&&'
     * @param $operator
     * @return string
     */
    protected function getOperatorInJS($operator)
    {
        if (array_key_exists($operator, LibraryExpressionFormType::$GROUP_TYPE_MAP_JS)) {
            return LibraryExpressionFormType::$GROUP_TYPE_MAP_JS[$operator];
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
        if (in_array($cmp, LibraryExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_MATH)) {
            return $this->getConditionInJSForMath($var, $cmp, $val);
        }

        //if STRING => format as 'func($var) . $real-cmp . $val', where func = $cmp['func'], $real-cmp = $cmp['cmp']
        if (array_key_exists($cmp, LibraryExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_STRING)) {
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
            $var . '.' . LibraryExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . LibraryExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['cmp'] . $val .
            ')';
        }

        // Below functions use regex, hence we have to remove the quotes from json
        // note: remove quotes and then json_encode to escape js with special chars and then remove quotes again due to json_encode
        $val = str_replace('"','', $val);
        // do escape js regex, not just concatenate string like this: (/' . $val . '/i)
        $val = json_encode($val);
        // Below functions use regex, hence we have to remove the quotes from json
        $val = str_replace('"','', $val);

        if ($cmp === 'contains') {
            return '(window.' .
            $var . '.' . LibraryExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $val . '/i) > -1' .
            ')';
        }

        if ($cmp === 'notContains') {
            return '(window.' .
            $var . '.' . LibraryExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $val . '/i) < 0' .
            ')';
        }

        if ($cmp === 'startsWith') {
            return '(window.' .
            $var . '.' . LibraryExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $val . '/i) === 0' .
            ')';
        }

        if ($cmp === 'notStartsWith') {
            return '(window.' .
            $var . '.' . LibraryExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $val . '/i) != 0' .
            ')';
        }

        if ($cmp === 'endsWith') {

            return '(window.' .
            $var . '.' . LibraryExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $val . '$/i) === (window.' . $var . '.length - "' . $val . '".length)' .
            ')';
        }

        if ($cmp === 'notEndsWith') {

            return '(window.' .
            $var . '.' . LibraryExpressionFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $val . '$/i) != (window.' . $var . '.length - "' . $val . '".length)' .
            ')';
        }

        return null;
    }
} 