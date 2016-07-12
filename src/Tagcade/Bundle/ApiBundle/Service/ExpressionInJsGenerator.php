<?php

namespace Tagcade\Bundle\ApiBundle\Service;


use Tagcade\Exception\InvalidFormatException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\ExpressionJsProducibleInterface;

class ExpressionInJsGenerator implements ExpressionInJsGeneratorInterface
{
    /*
     * "expressionDescriptor" example:
     * {
     *     "groupType":"AND",
     *     "groupVal":[
     *         {
     *             "var":"${USER_AGENT}", // using $INTERNAL_VARIABLE
     *             "cmp":"contains",
     *             "val":"blackberry",
     *             "type":"string"
     *         }
     *     ]
     * }
     */

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
    const  KEY_EXPRESSION_VAR = 'var';
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
        'length >', 'length <', 'length ==', 'length >=', 'length <=', 'length !=', 'is', 'isNot'
    ];

    static $EXPRESSION_CMP_VALUES_FOR_MATH = [
        '>', '<', '==', '>=', '<=', '!=', '===', '!=='
    ];

    static $EXPRESSION_CMP_VALUES_FOR_STRING = [
        'contains' => ['func' => 'search', 'cmp' => ''],
        'notContains' => ['func' => 'search', 'cmp' => ''],
        'startsWith' => ['func' => 'search', 'cmp' => ''],
        'notStartsWith' => ['func' => 'search', 'cmp' => ''],
        'endsWith' => ['func' => 'search', 'cmp' => ''],
        'notEndsWith' => ['func' => 'search', 'cmp' => ''],
        'length >' => ['func' => 'length', 'cmp' => '>'],
        'length <' => ['func' => 'length', 'cmp' => '<'],
        'length ==' => ['func' => 'length', 'cmp' => '=='],
        'length >=' => ['func' => 'length', 'cmp' => '>='],
        'length <=' => ['func' => 'length', 'cmp' => '<='],
        'length !=' => ['func' => 'length', 'cmp' => '!='],
        'is' => ['func' => 'search', 'cmp' => ''],
        'isNot' => ['func' => 'search', 'cmp' => ''],
    ];

    static $INTERNAL_VARIABLE_MAP = [
        '${PAGE_URL}' => '${PAGE_URL}', // location.href
        '${USER_AGENT}' => 'navigator.userAgent',
        '${SCREEN_WIDTH}' => 'top.screen.width',
        '${SCREEN_HEIGHT}' => 'top.screen.height',
        '${WINDOW_WIDTH}' => 'top.outerWidth',
        '${WINDOW_HEIGHT}' => 'top.outerHeight',
        '${DOMAIN}' => '${DOMAIN}', // top.location.hostname
        '${DEVICE}' => 'navigator.userAgent',
    ];

    static $SERVER_VARS = ['${COUNTRY}'];

    /**
     * Generate simple array containing keys that make js evaluation easier
     *
     * @param ExpressionJsProducibleInterface $expression
     * @return array['vars': [{'name': 'varName', 'type': 'string'}]  , 'expression'=>'')
     */
    public function generateExpressionInJs(ExpressionJsProducibleInterface $expression)
    {
        $descriptor = $expression->getDescriptor();

        if (null == $descriptor || count($descriptor) < 1) {
            return array('vars' => [], 'expression' => '');
        }

        $this->validateExpressionDescriptor($descriptor);

        return $this->simplifyExpression($descriptor);
    }

    /**
     * @param array $descriptor
     * @return array|\array[ [name:'', type: '], [name:'', type: ']] , 'expression'=>'')
     * @throws InvalidFormatException
     */
    public function generateExpressionInJsFromDescriptor(array $descriptor)
    {
        if (null == $descriptor || count($descriptor) < 1) {
            return array('vars' => [], 'expression' => '');
        }

        $this->validateExpressionDescriptor($descriptor);

        return $this->simplifyExpression($descriptor);
    }

    /**
     * simplify Expression, converting from descriptor structure with groupType, etc. into object containing string expression and all available variables
     *
     * @param array $expressionDescriptor
     * @return array('vars'=>[ [name:'', type: '], [name:'', type: ']] , 'expression'=>'')
     */
    protected function simplifyExpression(array $expressionDescriptor)
    {
        return $this->createExpressionObject($expressionDescriptor);
    }

    /**
     * create Expression String as '( A && (B || C) && D ...)'
     * @param array $expressionDescriptor
     * @return string
     */
    protected function createExpressionObject(array $expressionDescriptor)
    {
        //try to get groupType, if not null => is group, else is not group
        $groupType = (isset($expressionDescriptor[self::KEY_GROUP_TYPE])) ? self::$VAL_GROUPS[$expressionDescriptor[self::KEY_GROUP_TYPE]] : null;

        return ($groupType != null)
            ? $this->createExpressionAsGroupObject($groupType, $expressionDescriptor[self::KEY_GROUP_VAL])
            : $this->createExpressionAsConditionObject($expressionDescriptor);
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
                    function (array $expElement) use (&$vars) {

                        $exp = $this->createExpressionObject($expElement);
                        $vars = array_merge($vars, $exp['vars']);
                        return $exp['expression'];
                    },
                    $expressionAsGroup
                )
            )
            . ')';

        // filter unique
        $vars = array_unique($vars, SORT_REGULAR);
        $vars = array_values($vars);

        return ['vars' => $vars, 'expression' => $expString];
    }

    /**
     * create Other Expression String as condition as 'a > 1', 'b != 2', ...
     * @param array $expressionDescriptor
     * @return array('vars'=>, 'expression'=>)
     */
    protected function createExpressionAsConditionObject(array $expressionDescriptor)
    {
        $type = array_key_exists(self::KEY_EXPRESSION_TYPE, $expressionDescriptor) ? $expressionDescriptor[self::KEY_EXPRESSION_TYPE] : "";
        $val = $expressionDescriptor[self::KEY_EXPRESSION_VAL];

        if (null !== $type) {
            $type = strtolower($type);
        }

        switch ($type) {
            case 'string' :
                $val = '"' . $val . '"';
                break;
        }

        $exp = $this->getConditionInJS($expressionDescriptor[self::KEY_EXPRESSION_VAR], $expressionDescriptor[self::KEY_EXPRESSION_CMP], $val);

        return ['vars' => [['name' => $expressionDescriptor[self::KEY_EXPRESSION_VAR], 'type' => $type]], 'expression' => $exp];
    }

    /**
     * get Operator In JS. Return operator mapped from UI to Javascript syntax. e.g: 'AND' => '&&'
     * @param $operator
     * @return string
     */
    protected function getOperatorInJS($operator)
    {
        if (array_key_exists($operator, self::$GROUP_TYPE_MAP_JS)) {
            return self::$GROUP_TYPE_MAP_JS[$operator];
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
        if (in_array($cmp, self::$EXPRESSION_CMP_VALUES_FOR_MATH)) {
            return $this->getConditionInJSForMath($var, $cmp, $val);
        }

        //if STRING => format as 'func($var) . $real-cmp . $val', where func = $cmp['func'], $real-cmp = $cmp['cmp']
        if (array_key_exists($cmp, self::$EXPRESSION_CMP_VALUES_FOR_STRING)) {
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

        return '(' . $var . $cmp . $val . ')';
    }

    /**
     * get Converted Var
     * @param $var
     * @return mixed
     * - return original when SERVER_VAR,
     * - return internal mapped var when in $INTERNAL_VARIABLE_MAP,
     * - return SCOPE_VAR (as ${VAR(...) pattern} when others
     */
    private function getConvertedVar($var)
    {
        // if $SERVER_VARS => return original var
        if (in_array($var, self::$SERVER_VARS)) {
            return $var;
        }

        // if in $INTERNAL_VARIABLE_MAP, return mapped var
        // e.g: ${SCREEN_WIDTH} => return 'top.screen.width',
        if (isset(self::$INTERNAL_VARIABLE_MAP[$var]) && !empty(self::$INTERNAL_VARIABLE_MAP[$var])) {
            return self::$INTERNAL_VARIABLE_MAP[$var];
        }

        // others, return as $SCOPE var format
        // e.g: position => return ${VAR(position)}
        // note:
        // - "${VAR(a)}" will be translated to "window.a" for normal jsTag of ad slot by tagcade-js-tags module
        // - "${VAR(a)}" will be kept for hb jsTag of ad slot to support dynamicVars in tagcade bidder params
        return sprintf('${VAR(%s)}', $var);
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
            return '(' .
            $var . '.' . self::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . self::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['cmp'] . $val .
            ')';
        }

        // Below functions use regex, hence we have to remove the quotes from json
        // note: remove quotes and then json_encode to escape js with special chars and then remove quotes again due to json_encode
        $val = str_replace('"', '', $val);
        // do escape js regex, not just concatenate string like this: (/' . $val . '/i)
        $val = json_encode($val);
        // Below functions use regex, hence we have to remove the quotes from json
        $val = str_replace('"', '', $val);

        if ($cmp === 'contains') {
            return '(' .
            $var . '.' . self::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $val . '/i) > -1' .
            ')';
        }

        if ($cmp === 'notContains') {
            return '(' .
            $var . '.' . self::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $val . '/i) < 0' .
            ')';
        }

        if ($cmp === 'startsWith') {
            return '(' .
            $var . '.' . self::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $val . '/i) === 0' .
            ')';
        }

        if ($cmp === 'notStartsWith') {
            return '(' .
            $var . '.' . self::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $val . '/i) != 0' .
            ')';
        }

        if ($cmp === 'endsWith') {
            return '(' .
            $var . '.' . self::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $val . '$/i) === (' .  $var . '.length - "' . $val . '".length)' .
            ')';
        }

        if ($cmp === 'notEndsWith') {
            return '(' .
            $var . '.' . self::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $val . '$/i) != (' .  $var . '.length - "' . $val . '".length)' .
            ')';
        }

        if ($cmp === 'is') {
            $inList = explode(',', $val);

            if (!is_array($inList)) {
                return null;
            }

            $expForIn = '('; // contains... || contains...

            foreach ($inList as $inListIdx => $element) {
                $expForIn .= '(' .
                    $var . '.' . self::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $element . '/i) > -1' .
                    ')';

                if ($inListIdx < count($inList) - 1) {
                    $expForIn .= '||';
                }
            }

            $expForIn .= ')';

            return $expForIn;
        }

        if ($cmp === 'isNot') {
            $inList = explode(',', $val);

            if (!is_array($inList)) {
                return null;
            }

            $expForIn = '(';  // !contains... && !contains...

            foreach ($inList as $inListIdx => $element) {
                $expForIn .= '(' .
                    $var . '.' . self::$EXPRESSION_CMP_VALUES_FOR_STRING[$cmp]['func'] . '(/' . $element . '/i) < 0' .
                    ')';

                if ($inListIdx < count($inList) - 1) {
                    $expForIn .= '&&';
                }
            }

            $expForIn .= ')';

            return $expForIn;
        }

        return null;
    }

    /**
     * validate Expression which contains GROUP_AND / GROUP_OR and/or {'var', 'cmp', val}
     * @param mixed|array $expression
     * @throws InvalidFormatException if has one
     * - 'var', 'cmp', 'val' keys not set
     */
    public static function validateExpressionDescriptor(array $expression)
    {
        //check if is group
        if (array_key_exists(self::KEY_GROUP_TYPE, $expression)
            && array_key_exists(self::KEY_GROUP_VAL, $expression)
        ) {
            //check as recursive
            self::validateGroup($expression);
        } else {
            //is condition, then check if not match {'var', 'cmp', 'val'}
            if (!array_key_exists(self::KEY_EXPRESSION_VAR, $expression)
                || !array_key_exists(self::KEY_EXPRESSION_VAL, $expression)
                || !array_key_exists(self::KEY_EXPRESSION_CMP, $expression)
                || !array_key_exists(self::KEY_EXPRESSION_TYPE, $expression)
            ) {
                throw new InvalidFormatException('"' . self::KEY_EXPRESSION_VAR . '" or "' . self::KEY_EXPRESSION_CMP . '" or "' . self::KEY_EXPRESSION_VAL . '" or "' . self::KEY_EXPRESSION_TYPE . '" can not be empty');
            } else {
                self::validateCondition($expression);
            }
        }
    }

    /**
     * validate group
     * @param array $group
     * @throws InvalidFormatException if has one
     * - $group null or number of item less then GROUP_MIN_ITEM
     */
    public static function validateGroup(array $group)
    {
        //validate groupType
        $groupType = $group[self::KEY_GROUP_TYPE];
        if (
            null === $group
            || ($groupType !== self::VAL_GROUP_AND
                && $groupType !== self::VAL_GROUP_OR)
        ) {
            throw new InvalidFormatException('expect ' . self::KEY_GROUP_TYPE . ' is one of \'' . implode(', ', self::$VAL_GROUPS) . '\' of expression');
        }

        //validate number of items in groupVal
        $groupVal = $group[self::KEY_GROUP_VAL];
        if (!is_array($groupVal)
            || sizeof($groupVal) < 1
        ) {
            throw new InvalidFormatException('expect ' . self::KEY_GROUP_VAL . ' is array and has at least one expression');
        }

        //validate each expression (child) as recursive
        foreach ($groupVal as $expression) {
            self::validateExpressionDescriptor($expression);
        }
    }

    /**
     * validate Condition
     * @param array $expression
     * @throws InvalidFormatException if has one
     * - comparator of condition not supported
     */
    public static function validateCondition(array $expression)
    {
        //check each key to find un-supported keys
        foreach ($expression as $key => $value) {
            if (!in_array($key, self::$CONDITION_KEYS)) {
                throw new InvalidFormatException('expect only keys as \'' . self::KEY_EXPRESSION_VAR . '\', \'' . self::KEY_EXPRESSION_CMP . '\', \'' . self::KEY_EXPRESSION_VAL . '\', \'' . self::KEY_EXPRESSION_TYPE . '\' of expression');
            }
        }

        //it's ok formatted as {'var', 'cmp', 'val'}, now check each
        ////validate 'var'
        self::validateVar($expression[self::KEY_EXPRESSION_VAR]);

        ////validate 'cmp'
        self::validateCmp($expression[self::KEY_EXPRESSION_CMP]);

        self::validateType($expression[self::KEY_EXPRESSION_TYPE]);

        ////validate 'val'
        self::validateVal($expression[self::KEY_EXPRESSION_VAL], $expression[self::KEY_EXPRESSION_TYPE]);
    }

    /**
     * validate Var
     * @param $var
     * @throws InvalidFormatException if has one
     * - $var null or empty or invalid syntax as variable name
     */
    public static function validateVar($var)
    {
        if (!isset($var)
            || null == $var
            || 1 > sizeof($var)
        ) {
            throw new InvalidFormatException('expect \'' . self::KEY_EXPRESSION_VAR . '\' of condition');
        }

        //validate as javascript variable syntax
        if (!preg_match('/\${PAGE_URL}|\${USER_AGENT}|\${COUNTRY}|\${SCREEN_WIDTH}|\${SCREEN_HEIGHT}|\${WINDOW_WIDTH}|\${WINDOW_HEIGHT}|\${DOMAIN}|\${DEVICE}|^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $var)) {
            throw new InvalidFormatException('invalid variable name syntax of \'' . $var . '\' of condition');
        }
    }

    /**
     * validate Cmp
     * @param $cmp
     * @throws InvalidFormatException if has one
     * - $cmp null or empty or not supported
     */
    public static function validateCmp($cmp)
    {
        if (!isset($cmp)
            || null == $cmp
            || 1 > sizeof($cmp)
        ) {
            throw new InvalidFormatException('expect \'' . self::KEY_EXPRESSION_CMP . '\' of condition');
        }

        //check if in expression_cmp_values
        if (!in_array($cmp, self::$EXPRESSION_CMP_VALUES)) {
            throw new InvalidFormatException('not supported comparator as  \'' . $cmp . '\' of condition');
        }
    }

    /**
     * validate Type
     *
     * @param $type
     * @throws InvalidFormatException
     */
    public static function validateType($type)
    {
        $type = strtolower($type);

        if (!in_array($type, self::$SUPPORTED_DATA_TYPES)) {
            throw new InvalidFormatException('not supported data type \'' . $type . '\'');
        }
    }

    /**
     * validate Val
     * @param $val
     * @param $type
     * @throws InvalidFormatException if has one
     * - $variableDescriptorArray null or empty or cascade, injection, ...
     */
    public static function validateVal($val, $type = 'string')
    {
        $type = strtolower($type);

        switch ($type) {
            case 'string':
                //validate as escape syntax
                if (trim($val) != '' && preg_match("/[^a-zA-Z0-9_=@&!',:;#\.\$\+\*\(\)\[\]\-\/\?\s\|]/", $val))  {
                    throw new InvalidFormatException('not allow special characters (js injection) in \'' . $val . '\' of condition');
                }

                break;
            case 'numeric':
                if (!is_numeric($val)) {
                    throw new InvalidFormatException('type and value not matched');
                }
                break;
            case 'boolean':
                if (is_bool($val)) {
                    break;
                }

                $lowerVal = strtolower($val);
                if ($lowerVal != 'true' && $lowerVal != 'false') {
                    throw new InvalidFormatException('type and value not matched');
                }
                break;
        }
    }
}