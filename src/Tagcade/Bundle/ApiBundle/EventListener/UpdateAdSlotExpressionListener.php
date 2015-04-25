<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Exception\RuntimeException;
use Tagcade\Form\Type\AdSlotFormType;
use Tagcade\Model\Core\AdSlotInterface;

class UpdateAdSlotExpressionListener
{

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof AdSlotInterface || !$args->hasChangedField('variableDescriptor')) {
            return;
        }

        $this->updateExpressions($entity);
    }

    /**
     * handle event postPersist one site, this auto add site to SourceReportSiteConfig & SourceReportEmailConfig.
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof AdSlotInterface) {
            return;
        }

        $this->updateExpressions($entity);
    }

    /**
     * update Expressions, built from variableDescriptor automatically
     * @param AdSlotInterface $adSlot
     */
    protected function updateExpressions(AdSlotInterface $adSlot)
    {
        $descriptor = $adSlot->getVariableDescriptor();
        if (null == $descriptor || count($descriptor) < 1) {
            return;
        }

        $expressions = $descriptor[AdSlotFormType::KEY_EXPRESSIONS];

        $convertedExpressions = array_map(
            function (array $expression) {
                return $this->simplifyExpression($expression);
            },
            $expressions
        );

        $adSlot->setExpressions([AdSlotFormType::KEY_EXPRESSIONS => $convertedExpressions]);
    }

    /**
     * simplify Expression, make array of pair {expression, expectAdSlot}
     * @param array $expression
     * @return array
     */
    public function simplifyExpression(array $expression)
    {
        $currentExp = $expression[AdSlotFormType::KEY_EXPRESSION];
        $simplified = $this->createExpressionString($currentExp);

        return [AdSlotFormType::KEY_EXPRESSION => $simplified, AdSlotFormType::KEY_EXPECT_AD_SLOT => $expression[AdSlotFormType::KEY_EXPECT_AD_SLOT]];
    }

    /**
     * create Expression String as '( A && (B || C) && D ...)'
     * @param array $expression
     * @return string
     */
    protected function createExpressionString(array $expression)
    {
        //try to get groupType, if not null => is group, else is not group
        $groupType = (isset($expression[AdSlotFormType::KEY_GROUP_TYPE])) ? AdSlotFormType::$VAL_GROUPS[$expression[AdSlotFormType::KEY_GROUP_TYPE]] : null;

        return ($groupType != null)
            ? $this->createExpressionAsGroupString($groupType, $expression[AdSlotFormType::KEY_GROUP_VAL])
            : $this->createExpressionAsConditionString($expression);
    }

    /**
     * create Group (And/Or/...) Expression String as A &&/||/... B
     * @param $operator
     * @param array $expressionAsGroup
     * @return string
     */
    protected function createExpressionAsGroupString($operator, array $expressionAsGroup)
    {
        //not really needed? already verified before in formType?
        if ($expressionAsGroup == null || count($expressionAsGroup) < AdSlotFormType::GROUP_MIN_ITEM) {
            throw new RuntimeException('expect at least ' . AdSlotFormType::GROUP_MIN_ITEM . ' elements for AND/OR expression');
        }

        return '(' . implode(
            $this->getOperatorInJS($operator),
            array_map(
                function (array $expElement) {
                    return $this->createExpressionString($expElement);
                },
                $expressionAsGroup
            )
        )
        . ')';
    }

    /**
     * create Other Expression String as condition as 'a > 1', 'b != 2', ...
     * @param array $expression
     * @return string
     */
    protected function createExpressionAsConditionString(array $expression)
    {
        $type = array_key_exists(AdSlotFormType::KEY_EXPRESSION_TYPE, $expression) ? $expression[AdSlotFormType::KEY_EXPRESSION_TYPE] : "";
        $val = $expression[AdSlotFormType::KEY_EXPRESSION_VAL];

        if(null !== $type) {
            $type = strtolower($type);
        }

        switch ($type) {
            case 'string' :
                $val = '"' . $val . '"';
                break;
        }

        return $this->getConditionInJS($expression[AdSlotFormType::KEY_EXPRESSION_VAR], $expression[AdSlotFormType::KEY_EXPRESSION_CMP], $val);
    }

    /**
     * get Operator In JS. Return operator mapped from UI to Javascript syntax. e.g: 'AND' => '&&'
     * @param $operator
     * @return string
     */
    protected function getOperatorInJS($operator)
    {
        if (array_key_exists($operator, AdSlotFormType::$GROUP_TYPE_MAP_JS)) {
            return AdSlotFormType::$GROUP_TYPE_MAP_JS[$operator];
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
        //if MATH => format as '$var . $cmp . $val'
        if (in_array($cmp, AdSlotFormType::$EXPRESSION_CMP_VALUES_FOR_MATH)) {
            return $this->getConditionInJSForMath($var, $cmp, $val);
        }

        //if STRING => format as 'func($var) . $real-cmp . $val', where func = $cmp['func'], $real-cmp = $cmp['cmp']
        if (array_key_exists($cmp, AdSlotFormType::$EXPRESSION_CMP_VALUES_FOR_STRING)) {
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
        return '(' . $var . $cmp . $val . ')';
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
        //return '$var.length . $real-cmp . $val'; e.g: 'a.length > 1'
        if (strpos($cmp, 'length') !== false) { //do not use '!strpos()'
            return '(' .
            $var . '.' . AdSlotFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . AdSlotFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['cmp'] . $val .
            ')';
        }

        //return '$var.func($val) . $real-cmp . -1; e.g: 'a.startsWith(3) > -1'
        if ($cmp === 'startsWith'
            || $cmp === 'endsWith'
            || $cmp === 'contains'
        ) {
            return '(' .
            $var . '.' . AdSlotFormType::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(' . $val . ') > -1' .
            ')';
        }

        return null;
    }
}