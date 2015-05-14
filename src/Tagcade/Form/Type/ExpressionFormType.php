<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\AdSlot;
use Tagcade\Entity\Core\Expression;
use Tagcade\Exception\InvalidFormException;
use Tagcade\Model\Core\ExpressionInterface;


class ExpressionFormType extends AbstractRoleSpecificFormType
{
    /** key expressions */
    const KEY_EXPRESSIONS = 'expressions';

    /** key expression in expressions */
    const KEY_EXPRESSION_DESCRIPTOR = 'expressionDescriptor';
    /** key expectAdSlot in expressions */
    const KEY_EXPECT_AD_SLOT = 'expectAdSlot';
    /** key startingPosition in expressions */
    const KEY_STARTING_POSITION = 'startingPosition';

    /** key groupType in expression */
    const KEY_GROUP_TYPE = 'groupType';
    /** key groupVal in expression */
    const KEY_GROUP_VAL = 'groupVal';

    /** value for groupType */
    const VAL_GROUP_AND = 'AND';
    const VAL_GROUP_OR = 'OR';

    /** min item in groupVal */
    const GROUP_MIN_ITEM = 2;

    /** key var in an expression as a condition */
    const KEY_EXPRESSION_VAR = 'var';
    /** key cmp in an expression as a condition */
    const KEY_EXPRESSION_CMP = 'cmp';
    /** key val in an expression as a condition */
    const KEY_EXPRESSION_VAL = 'val';
    /** key type in an expression as a condition */
    const KEY_EXPRESSION_TYPE = 'type';

    /** values array for groupType */
    static $VAL_GROUPS = [
        self::VAL_GROUP_AND => self::VAL_GROUP_AND,
        self::VAL_GROUP_OR => self::VAL_GROUP_OR
    ];

    /** map values from groupType to js*/
    static $GROUP_TYPE_MAP_JS = [
        self::VAL_GROUP_AND => '&&',
        self::VAL_GROUP_OR => '||'
    ];

    /** keys values array for an expression as a condition */
    static $CONDITION_KEYS = [
        self::KEY_EXPRESSION_VAR,
        self::KEY_EXPRESSION_CMP,
        self::KEY_EXPRESSION_VAL,
        self::KEY_EXPRESSION_TYPE
    ];

    static $SUPPORTED_DATA_TYPES = [
        'string',
        'boolean',
        'numeric'
    ];

    /** keys values array for an expression as a condition */
    static $EXPRESSION_CMP_VALUES = [
        //MATH
        '>', '<', '==', '>=', '<=', '!=', '===', '!==',
        //STRING
        'contains', 'startsWith', 'endsWith', 'notContains', 'notEndsWith', 'notStartsWith',
        'length >', 'length <', 'length ==', 'length >=', 'length <=', 'length !='
    ];

    static $EXPRESSION_CMP_VALUES_FOR_MATH = [
        '>', '<', '==', '>=', '<=', '!=', '===', '!=='
    ];

    static $EXPRESSION_CMP_VALUES_FOR_STRING = [
        'contains'     => ['func' => 'search',    'cmp' => ''],
        'notContains' => ['func' => 'search',    'cmp' => ''],
        'startsWith'   => ['func' => 'search', 'cmp' => ''],
        'notStartsWith' => ['func' => 'search', 'cmp' => ''],
        'endsWith'     => ['func' => 'search',   'cmp' => ''],
        'notEndsWith'     => ['func' => 'search',   'cmp' => ''],
        'length >'     => ['func' => 'length',     'cmp' => '>'],
        'length <'     => ['func' => 'length',     'cmp' => '<'],
        'length =='    => ['func' => 'length',     'cmp' => '=='],
        'length >='    => ['func' => 'length',     'cmp' => '>='],
        'length <='    => ['func' => 'length',     'cmp' => '<='],
        'length !='    => ['func' => 'length',     'cmp' => '!='],
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                try {
                    /**
                     * @var ExpressionInterface $expression
                     */
                    $expression = $event->getData();
                    if (null === $expression->getExpectAdSlot()) {
                        throw new InvalidFormException('expectedAdSlot does not exist');
                    }

                    if (null === $expression->getExpressionDescriptor()
                        || !is_array($expression->getExpressionDescriptor())
                    ) {
                        throw new InvalidFormException('expressionDescriptor null or not is array');
                    }

                    $this->validateExpressionDescriptor($expression->getExpressionDescriptor());

                } catch (InvalidFormException $ex) {
                    $form = $event->getForm();
                    $form->get(self::KEY_EXPRESSION_DESCRIPTOR)->addError(new FormError($ex->getMessage()));
                }
            }
        );

        $builder
            ->add('expressionDescriptor')
            ->add('expectAdSlot', 'entity', ['class' => AdSlot::class])
            ->add('startingPosition')
        ;
    }

    /**
     * validate Expression which contains GROUP_AND / GROUP_OR and/or {'var', 'cmp', val}
     * @param mixed|array $expression
     * @throws InvalidFormException if has one
     * - 'var', 'cmp', 'val' keys not set
     */
    private function validateExpressionDescriptor(array $expression)
    {
        //check if is group
        if (isset($expression[self::KEY_GROUP_TYPE])
            && isset($expression[self::KEY_GROUP_VAL])
        ) {
            //check as recursive
            $this->validateGroup($expression);
        } else {
            //is condition, then check if not match {'var', 'cmp', 'val'}
            if (!isset($expression[self::KEY_EXPRESSION_VAR])
                || !isset($expression[self::KEY_EXPRESSION_CMP])
                || !isset($expression[self::KEY_EXPRESSION_VAL])
            ) {
                throw new InvalidFormException('expect \'' . self::KEY_EXPRESSION_VAR . '\', \'' . self::KEY_EXPRESSION_VAR . '\', \'' . self::KEY_EXPRESSION_VAR . '\' of expression');
            } else {
                $this->validateCondition($expression);
            }
        }
    }

    /**
     * validate group
     * @param array $group
     * @throws InvalidFormException if has one
     * - $group null or number of item less then GROUP_MIN_ITEM
     */
    private function validateGroup(array $group)
    {
        //validate groupType
        $groupType = $group[self::KEY_GROUP_TYPE];
        if (
            null === $group
            || ($groupType !== self::VAL_GROUP_AND
                && $groupType !== self::VAL_GROUP_OR)
        ) {
            throw new InvalidFormException('expect ' . self::KEY_GROUP_TYPE . ' is one of \'' . implode(', ', self::$VAL_GROUPS) . '\' of expression');
        }

        //validate number of items in groupVal
        $groupVal = $group[self::KEY_GROUP_VAL];
        if (!is_array($groupVal)
            || sizeof($groupVal) < 1
        ) {
            throw new InvalidFormException('expect ' . self::KEY_GROUP_VAL . ' is array and has at least one expression');
        }

        //validate each expression (child) as recursive
        foreach ($groupVal as $expression) {
            $this->validateExpressionDescriptor($expression);
        }
    }

    /**
     * validate Condition
     * @param array $expression
     * @throws InvalidFormException if has one
     * - comparator of condition not supported
     */
    private function validateCondition(array $expression)
    {
        //check each key to find un-supported keys
        foreach ($expression as $key => $value) {
            if (!in_array($key, self::$CONDITION_KEYS)) {
                throw new InvalidFormException('expect only keys as \'' . self::KEY_EXPRESSION_VAR . '\', \'' . self::KEY_EXPRESSION_CMP . '\', \'' . self::KEY_EXPRESSION_VAL. '\', \'' . self::KEY_EXPRESSION_TYPE . '\' of expression');
            }
        }

        //it's ok formatted as {'var', 'cmp', 'val'}, now check each
        ////validate 'var'
        $this->validateVar($expression[self::KEY_EXPRESSION_VAR]);

        ////validate 'cmp'
        $this->validateCmp($expression[self::KEY_EXPRESSION_CMP]);

        $this->validateType($expression[self::KEY_EXPRESSION_TYPE]);

        ////validate 'val'
        $this->validateVal($expression[self::KEY_EXPRESSION_VAL], $expression[self::KEY_EXPRESSION_TYPE]);
    }


    /**
     * validate Var
     * @param $var
     * @throws InvalidFormException if has one
     * - $var null or empty or invalid syntax as variable name
     */
    private function validateVar($var)
    {
        if (!isset($var)
            || null == $var
            || 1 > sizeof($var)
        ) {
            throw new InvalidFormException('expect \'' . self::KEY_EXPRESSION_VAR . '\' of condition');
        }

        //validate as javascript variable syntax
        if (!preg_match('/\${PAGEURL}|^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $var)) {
            throw new InvalidFormException('invalid variable name syntax of \'' . $var . '\' of condition');
        }
    }

    /**
     * validate Cmp
     * @param $cmp
     * @throws InvalidFormException if has one
     * - $cmp null or empty or not supported
     */
    private function validateCmp($cmp)
    {
        if (!isset($cmp)
            || null == $cmp
            || 1 > sizeof($cmp)
        ) {
            throw new InvalidFormException('expect \'' . self::KEY_EXPRESSION_CMP . '\' of condition');
        }

        //check if in expression_cmp_values
        if (!in_array($cmp, self::$EXPRESSION_CMP_VALUES)) {
            throw new InvalidFormException('not supported comparator as  \'' . $cmp . '\' of condition');
        }
    }

    private function validateType($type) {
        $type = strtolower($type);

        if (!in_array($type, self::$SUPPORTED_DATA_TYPES)) {
            throw new InvalidFormException('not supported data type \'' . $type . '\'');
        }
    }
    /**
     * validate Val
     * @param $val
     * @param $type
     * @throws InvalidFormException if has one
     * - $variableDescriptorArray null or empty or cascade, injection, ...
     */
    private function validateVal($val, $type ='string')
    {
//        if (!isset($val)
//            || null == $val
//            || 1 > sizeof($val)
//        ) {
//            throw new InvalidFormException('expect \'' . self::KEY_EXPRESSION_VAL . '\' of condition');
//        }

        $type = strtolower($type);

        switch ($type) {
            case 'string':
                //validate as escape syntax
                if (!preg_match('/^[a-zA-Z0-9_\x7f-\xff\s][a-zA-Z0-9_\x7f-\xff\s]*$/', $val)) {
                    throw new InvalidFormException('not allow special characters (js injection) in \'' . $val . '\' of condition');
                }

                break;
            case 'numeric':
                if (!is_numeric($val)) {
                    throw new InvalidFormException('type and value not matched');
                }
                break;
            case 'boolean':
                if (is_bool($val)) {
                    break;
                }

                $lowerVal = strtolower($val);
                if ($lowerVal != 'true' && $lowerVal != 'false') {
                    throw new InvalidFormException('type and value not matched');
                }
                break;
        }

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Expression::class,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_expression';
    }
}