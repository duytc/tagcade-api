<?php

namespace Tagcade\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tagcade\Entity\Core\AdSlot;
use Tagcade\Entity\Core\Site;
use Tagcade\Exception\InvalidFormException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;

class AdSlotFormType extends AbstractRoleSpecificFormType
{
    /** key expressions */
    const KEY_EXPRESSIONS = 'expressions';

    /** key expression in expressions */
    const KEY_EXPRESSION = 'expression';
    /** key expectAdSlot in expressions */
    const KEY_EXPECT_AD_SLOT = 'expectAdSlot';

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
        'contains', 'startsWith', 'endsWith',
        'length >', 'length <', 'length ==', 'length >=', 'length <=', 'length !='
    ];

    static $EXPRESSION_CMP_VALUES_FOR_MATH = [
        '>', '<', '==', '>=', '<=', '!=', '===', '!=='
    ];

    static $EXPRESSION_CMP_VALUES_FOR_STRING = [
        'contains'     => ['func' => 'indexOf',    'cmp' => ''],
        'startsWith'   => ['func' => 'startsWith', 'cmp' => ''],
        'endsWith'     => ['func' => 'endsWith',   'cmp' => ''],
        'length >'     => ['func' => 'length',     'cmp' => '>'],
        'length <'     => ['func' => 'length',     'cmp' => '<'],
        'length =='    => ['func' => 'length',     'cmp' => '=='],
        'length >='    => ['func' => 'length',     'cmp' => '>='],
        'length <='    => ['func' => 'length',     'cmp' => '<='],
        'length !='    => ['func' => 'length',     'cmp' => '!='],
    ];

    /** @var AdSlotRepositoryInterface */
    private $adSlotRepository;

    function __construct(AdSlotRepositoryInterface $adSlotRepository)
    {
        $this->adSlotRepository = $adSlotRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->userRole instanceof AdminInterface) {

            // allow all sites, default is fine
            $builder->add('site');

        } else if ($this->userRole instanceof PublisherInterface) {

            // for publishers, only allow their sites
            $builder
                ->add('site', 'entity', [
                    'class' => Site::class,
                    'query_builder' => function (SiteRepositoryInterface $repository) {
                        /** @var PublisherInterface $publisher */
                        $publisher = $this->userRole;

                        return $repository->getSitesForPublisherQuery($publisher);
                    }
                ]);

        } else {
            throw new LogicException('A valid user role is required by AdSlotFormType');
        }

        $builder
            ->add('name')
            ->add('width')
            ->add('height')
            ->add('enableVariable')
            ->add('variableDescriptor')//
            //->add('expressions') //not add expressions to form because this automatically built and updated from variableDescriptor
        ;

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var AdSlotInterface $data */
                $data = $event->getData();

                //validate variableDescriptor
                $variableDescriptor = $data->getVariableDescriptor();
                if (null !== $variableDescriptor && 0 < sizeof($variableDescriptor) && $data->getEnableVariable()) {
                    try {
                        $this->validateVariableDescriptor($variableDescriptor);
                    } catch (InvalidFormException $ex) {
                        $form = $event->getForm();
                        $form->get('variableDescriptor')->addError(new FormError($ex->getMessage()));
                    }
                }
            }
        );
    }

    /**
     * validate VariableDescriptor, formatted as:
     *
     * variableDescriptor:
     * {
     *    "expressions":[
     *       {
     *          "expression":{
     *             "groupType":"AND",
     *             "groupVal":
     *             [
     *                {
     *                   "var":"a",
     *                   "cmp":">",
     *                   "val":1
     *                },
     *                {
     *                   "groupType":"OR",
     *                   "groupVal":
     *                   [
     *                      {
     *                         "var":"b",
     *                         "cmp":"<=",
     *                         "val":2
     *                      },
     *                      {
     *                         "var":"c",
     *                         "cmp":"==",
     *                         "val":true
     *                      },
     *                      {...}
     *                   ]
     *                },
     *                {
     *                   "var":"d",
     *                   "cmp":"!=",
     *                   "val":"abc"
     *                },
     *                {...}
     *             ]
     *          },
     *          "expectAdSlot":1
     *       },
     *       {
     *          "expression":{
     *             "groupType":"AND",
     *             "groupVal":
     *             [
     *                {
     *                   "var":"e",
     *                   "cmp":">",
     *                   "val":1
     *                },
     *                {
     *                   "var":"f",
     *                   "cmp":">",
     *                   "val":1
     *                },
     *                {...}
     *             ]
     *          },
     *          "expectAdSlot":2
     *       },
     *       {...}
     *    ]
     * }
     *
     * where 'AND'/'OR' [{...},...] is called as group, {"var", "cmp", "val"} is called as condition
     *
     * @param array $variableDescriptorArray as json array
     * @throws InvalidFormException if has one
     * - $variableDescriptorArray null or empty
     * - 'defaultVal' key not existed
     * - 'expressions' key not existed
     */
    private function validateVariableDescriptor(array $variableDescriptorArray)
    {
        //validate null or empty
        if (null === $variableDescriptorArray
            || 1 > sizeof($variableDescriptorArray)
        ) {
            throw new InvalidFormException('expect variableDescriptor not null and not empty');
        }

        //validate expressions
        if (null === $variableDescriptorArray
            || !isset($variableDescriptorArray[self::KEY_EXPRESSIONS])
        ) {
            throw new InvalidFormException('expect \'' . self::KEY_EXPRESSIONS . '\' of variableDescriptor');
        }

        $expressions = $variableDescriptorArray[self::KEY_EXPRESSIONS];
        $this->validateExpressions($expressions);
    }

    /**
     * validate expressions
     * @param array $expressions
     * @throws InvalidFormException if has one
     * - $expressions null or empty
     * - 'expectAdSlot' key not existed
     * - 'expression' key not existed
     */
    private function validateExpressions($expressions)
    {
        //validate expressions self (only check null, is_array, ...)
        if (!isset($expressions)
            || null == $expressions
            || !is_array($expressions)
            || 1 > sizeof($expressions)
        ) {
            throw new InvalidFormException('expect \'' . self::KEY_EXPRESSIONS . '\' of expressions');
        }

        //validate each of expressions sequent as {expression, expectAdSlot} values
        for ($i = 0; $i < sizeof($expressions); $i++) {
            //validate each expectAdSlot
            if (null === $expressions[$i]
                || !isset($expressions[$i][self::KEY_EXPECT_AD_SLOT])
            ) {
                throw new InvalidFormException('expect \'' . self::KEY_EXPECT_AD_SLOT . '\' of expressions');
            }

            $expectAdSlot = $expressions[$i][self::KEY_EXPECT_AD_SLOT];
            $this->validateExpectAdSlot($expectAdSlot);

            //validate each expression
            if (null === $expressions[$i]
                || !isset($expressions[$i][self::KEY_EXPRESSION])
            ) {
                throw new InvalidFormException('expect \'' . self::KEY_EXPRESSIONS . '\' of expressions');
            }

            $expression = $expressions[$i][self::KEY_EXPRESSION];
            $this->validateExpression($expression);
        }
    }

    /**
     * validate Expression which contains GROUP_AND / GROUP_OR and/or {'var', 'cmp', val}
     * @param mixed|array $expression
     * @throws InvalidFormException if has one
     * - 'var', 'cmp', 'val' keys not set
     */
    private function validateExpression(array $expression)
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
            || self::GROUP_MIN_ITEM > sizeof($groupVal)
        ) {
            throw new InvalidFormException('expect ' . self::KEY_GROUP_VAL . ' is array and has minimum items are ' . self::GROUP_MIN_ITEM . 'of expression');
        }

        //validate each expression (child) as recursive
        foreach ($groupVal as $expression) {
            $this->validateExpression($expression);
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
     * validate expectAdSlot
     * @param mixed $expectAdSlot
     * @throws InvalidFormException if has one
     * - $expectAdSlot null or empty
     */
    private function validateExpectAdSlot($expectAdSlot)
    {
        if (!isset($expectAdSlot)
            || null == $expectAdSlot
            || 1 > sizeof($expectAdSlot)
        ) {
            throw new InvalidFormException('expect \'' . self::KEY_EXPECT_AD_SLOT . '\' of condition');
        }

        //check if expectAdSlot existed in db
        if(null === $this->adSlotRepository->find($expectAdSlot)){
            throw new InvalidFormException('not found \'' . self::KEY_EXPECT_AD_SLOT . '\' id #' . $expectAdSlot . ' of condition');
        }
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
        if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $var)) {
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
        if (!isset($val)
            || null == $val
            || 1 > sizeof($val)
        ) {
            throw new InvalidFormException('expect \'' . self::KEY_EXPRESSION_VAL . '\' of condition');
        }

        $type = strtolower($type);

        switch ($type) {
            case 'string':
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
        //validate as escape syntax
        if (!preg_match('/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $val)) {
            throw new InvalidFormException('not allow special characters (js injection) in \'' . $val . '\' of condition');
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => AdSlot::class,
            ]);
    }

    public function getName()
    {
        return 'tagcade_form_ad_slot';
    }
}