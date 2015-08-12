<?php

class DynamicAdSlot
{

    static $JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT = [];

    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());

        self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT = [
            'site' => PARAMS_SITE,
            'libraryAdSlot' => [
                'name' => 'dynamicAdSlot-test',
                'libraryExpressions' => [ //allow empty
                    [
                        'expressionDescriptor' => [
                            'groupType' => 'AND',
                            'groupVal' => [
                                [
                                    'var' => 'checkLength',
                                    'cmp' => 'length >=',
                                    'val' => 10,
                                    'type' => 'numeric'
                                ]
                            ]
                        ],
                        'expectLibraryAdSlot' => PARAMS_LIBRARY_EXPECTED_AD_SLOT,
                        'expressions' => [
                            [
                                'expectAdSlot' => PARAMS_EXPECTED_AD_SLOT,
                            ],
                        ],
                        'startingPosition' => 1
                    ],
                ],
                //'native' => false, //default: false
                'defaultLibraryAdSlot' => PARAMS_LIBRARY_DEFAULT_AD_SLOT,
            ],
            'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT
        ];
    }

    public function _after(ApiTester $I)
    {
    }

    /**
     * get All DynamicAdSlot
     * @param ApiTester $I
     */
    public function getAllDynamicAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get DynamicAdSlot By Id
     * @param ApiTester $I
     */
    public function getDynamicAdSlotById(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots/' . PARAMS_DYNAMIC_AD_SLOT);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get DynamicAdSlot By Id Not Existed
     * @param ApiTester $I
     */
    public function getDynamicAdSlotByIdNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots/' . '-1');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }

    /**
     * add DynamicAdSlot
     * @param ApiTester $I
     */
    public function addDynamicAdSlot(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        // full expressionDescriptor
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor'] = [
            'groupType' => 'AND',
            'groupVal' => [
                [
                    'var' => 'checkLength',
                    'cmp' => 'length >=',
                    'val' => 10,
                    'type' => 'numeric'
                ],
                [
                    'groupType' => 'OR',
                    'groupVal' => [
                        [
                            'var' => 'checkMath',
                            'cmp' => '<=',
                            'val' => 2,
                            'type' => 'numeric'
                        ],
                        [
                            'var' => 'checkBoolean',
                            'cmp' => '==',
                            'val' => 'true',
                            'type' => 'boolean'
                        ]
                    ]
                ],
                [
                    'var' => 'checkString',
                    'cmp' => '!=',
                    'val' => 'abc',
                    'type' => 'string'
                ]
            ]
        ];

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(201);
    }

    /**
     * add DynamicAdSlot failed by Missing Site
     * @param ApiTester $I
     */
    public function addDynamicAdSlotFailedByMissingSite(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //remove 'site'
        unset($jsonData['site']);

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by Site null
     * @param ApiTester $I
     */
    public function addDynamicAdSlotFailedBySiteNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'site' null
        $jsonData['site'] = null;

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by Site not existed
     * @param ApiTester $I
     */
    public function addDynamicAdSlotFailedBySiteNotExisted(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'site' not existed
        $jsonData['site'] = -1;

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by Missing LibraryAdSlot
     * @param ApiTester $I
     */
    public function addDynamicAdSlotFailedByMissingLibraryAdSlot(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'site' null
        unset($jsonData['libraryAdSlot']);

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by LibraryAdSlot null
     * @param ApiTester $I
     */
    public function addDynamicAdSlotFailedByLibraryAdSlotNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot' null
        $jsonData['libraryAdSlot'] = null;

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by LibraryAdSlot is not json_array
     * @param ApiTester $I
     */
    public function addDynamicAdSlotFailedByLibraryAdSlotNotJsonArray(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot' this is not json_array
        $jsonData['libraryAdSlot'] = 'libraryAdSlot_not_json_array';

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by Missing DefaultAdSlot Only when have LibraryExpressions
     * @param ApiTester $I
     */
    public function addDynamicAdSlotFailedByMissingDefaultAdSlotOnly(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'defaultAdSlot' missing
        unset($jsonData['defaultAdSlot']);

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        //$I->seeResponseCodeIs(400);
        $I->seeResponseCodeIs(201); //allow, because already have LibraryExpressions
    }

    /**
     * add DynamicAdSlot failed by DefaultAdSlot null Only when have LibraryExpressions
     * @param ApiTester $I
     */
    public function addDynamicAdSlotFailedByDefaultAdSlotNullOnly(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'defaultAdSlot' null
        $jsonData['defaultAdSlot'] = null;

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        //$I->seeResponseCodeIs(400);
        $I->seeResponseCodeIs(201); //allow, because already have LibraryExpressions
    }

    /**
     * add DynamicAdSlot failed by DefaultAdSlot not existed
     * @param ApiTester $I
     */
    public function addDynamicAdSlotFailedByDefaultAdSlotNotExisted(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'DefaultAdSlot' not existed
        $jsonData['defaultAdSlot'] = -1;
        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by defaultAdSlot wrong dataType
     * @param ApiTester $I
     */
    public function addDynamicAdSlotFailedByWrongDataType(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'DefaultAdSlot' wrong data type
        $jsonData['defaultAdSlot'] = '3_wrong';

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by name null
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithNameNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot name' null
        $jsonData['libraryAdSlot']['name'] = null;

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        //$I->seeResponseCodeIs(201);
        $I->seeResponseCodeIs(400); //now not allow
    }

    /**
     * add dynamicAdSlot failed caused by expressions null only when have DefaultLibraryAdSlot or DefaultAdSlot
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionNullOnly(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot expressions' null
        $jsonData['libraryAdSlot']['libraryExpressions'] = null;

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(201); //allow if has defaultLibraryAdSlot or defaultAdSlot
        //$I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by both defaultAdSlot and expressions null
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithBothDefaultAdSLotAndLibraryExpressionNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot expressions' null
        $jsonData['defaultAdSlot'] = null;
        //'libraryAdSlot expressions' null
        $jsonData['libraryAdSlot']['libraryExpressions'] = null;

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400); //not allow, at least one of defaultAdSlot and LibraryExpressions not null
    }

    /**
     * add dynamicAdSlot failed caused by expressions is not json_array
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionNotJsonArray(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions' not json_array
        $jsonData['libraryAdSlot']['libraryExpressions'] = 'libraryExpressions_not_json_array';

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by expressions missing descriptor
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionMissingDescriptor(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor' null
        unset($jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']);

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor not json_array
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionDescriptorNotJsonArray(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor' not json_data
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor'] = 'expressionDescriptor_not_json_data';

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor missing groupType
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionDescriptorMissingGroupType(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupType' missing
        unset($jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupType']);

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupType null
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionDescriptorGroupTypeNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupType' null
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupType'] = null;

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupType not supported
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionDescriptorGroupTypeNotSupported(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupType' null
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupType'] = 'NOT_SUPPORTED';

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor missing groupVal
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionDescriptorMissingGroupVal(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal' missing
        unset($jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal']);

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupVal null
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionDescriptorGroupValNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal' null
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'] = null;

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupVal not json_array
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionDescriptorGroupValNotJsonArray(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal' not json_array
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'] = 'groupVal_not_json_array';

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupVal Var missing
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionDescriptorGroupValMissingVar(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal var' missing
        unset($jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['var']);

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupVal Var invalid
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionDescriptorGroupValVarInvalid(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal var' invalid
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['var'] = '~!@#$%^&*()_INVALID';

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupVal Cmp missing
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionDescriptorGroupValMissingCmp(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal cmp' missing
        unset($jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['cmp']);

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupVal Cmp not supported
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionDescriptorGroupValCmpNotSupported(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal cmp' not supported
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['cmp'] = 'NOT_SUPPORTED';

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupVal Val missing
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionDescriptorGroupValMissingVal(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal val' missing
        unset($jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['val']);

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupVal Type missing
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionDescriptorGroupValMissingType(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal type' missing
        unset($jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['type']);

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupVal type not supported
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionDescriptorGroupValTypeNotSupported(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal type' not supported
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['type'] = 'NOT_SUPPORTED';

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupVal type and val incompatible
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionDescriptorGroupValTypeIncompatible(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal' type and val incompatible
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['type'] = 'numeric';
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['val'] = '123_string';

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupVal contains unexpected field
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionDescriptorGroupValContainsUnexpectedField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal' contains unexpected field
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['unexpected_field'] = 'value';

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor contains unexpected field
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionDescriptorContainsUnexpectedField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal' contains unexpected field
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['unexpected_field'] = 'value';

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by libraryExpressions missing expressions
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionInvalidByMissingExpressions(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expectAdSlot' null
        unset($jsonData['libraryAdSlot']['libraryExpressions'][0]['expressions']);

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by libraryExpressions expressions null
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionInvalidByExpressionsNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expectAdSlot' null
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressions'] = null;

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by libraryExpressions expressions not json_array
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionInvalidByExpressionsNotJsonArray(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expectAdSlot' null
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressions'] = 'expressions_not_json_array';

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by libraryExpressions's Expressions missing expectAdSlot
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionInvalidByExpressionsMissingExpectAdSlot(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expectAdSlot' null
        unset($jsonData['libraryAdSlot']['libraryExpressions'][0]['expressions']['expectAdSlot']);

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by libraryExpressions Expressions expectAdSlot null
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionInvalidByExpressionsExpectAdSlotNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expectAdSlot' null
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressions']['expectAdSlot'] = null;

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by libraryExpressions Expressions expectAdSlot not existed
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionInvalidByExpressionsExpectAdSlotNotExisted(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expectAdSlot' not existed
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressions']['expectAdSlot'] = -1;

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by libraryExpressions missing startingPosition
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionInvalidByMissingStartingPosition(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions startingPosition' missing
        unset($jsonData['libraryAdSlot']['libraryExpressions'][0]['startingPosition']);

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(201); //allow because api auto-correct
        //$I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by libraryExpressions startingPosition null
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionInvalidByStartingPositionNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions startingPosition' null
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['startingPosition'] = null;

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(201); //allow because api auto-correct
        //$I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by libraryExpressions invalid at adSlot not existed
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionInvalidByStartingPositionInvalid(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions startingPosition' invalid
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['startingPosition'] = -1;

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(201); //allow because api auto-correct
        //$I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by InCompatible Native AdSlot for Expressions expectAdSlot
     * @param ApiTester $I
     */
    public function addDynamicAdSlotFailedByInCompatibleNativeAdSlotForExpressionsExpectAdSlot(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions' native and expectAdSlot incompatible
        //$jsonData['libraryAdSlot']['native'] = false; //make sure not supported native. TODO: unknown the way to set this to 'false' as boolean. Codeception always convert to string :(
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressions']['expectAdSlot'] = PARAMS_NATIVE_AD_SLOT; //native ad slot

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by InCompatible Native AdSlot for defaultAdSlot
     * @param ApiTester $I
     */
    public function addDynamicAdSlotFailedByInCompatibleNativeAdSlotForDefaultAdSlot(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions' native and defaultAdSlot incompatible
        //$jsonData['libraryAdSlot']['native'] = false; //make sure not supported native. TODO: unknown the way to set this to 'false' as boolean. Codeception always convert to string :(
        $jsonData['defaultAdSlot'] = PARAMS_NATIVE_AD_SLOT; //native ad slot

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by libraryAdSlot Expression's element contains unexpected field
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryExpressionInvalidByElementContainsUnexpectedField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryExpressions[0]' contains unexpected field
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['unexpected_field'] = 'value'; //unexpected field

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by Missing LibraryAdSlot DefaultLibraryAdSlot only when have LibraryExpressions
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryAdSlotInvalidByMissingDefaultLibraryAdSlotOnly(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions defaultLibraryAdSlot' missing
        unset($jsonData['libraryAdSlot']['defaultLibraryAdSlot']);

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        //$I->seeResponseCodeIs(400);
        $I->seeResponseCodeIs(201); //allow, because already have LibraryExpressions
    }

    /**
     * add DynamicAdSlot failed by LibraryAdSlot DefaultLibraryAdSlot null only when have LibraryExpressions
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryAdSlotInvalidByDefaultLibraryAdSlotNullOnly(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions defaultLibraryAdSlot' null
        $jsonData['libraryAdSlot']['defaultLibraryAdSlot'] = null;

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        //$I->seeResponseCodeIs(400);
        $I->seeResponseCodeIs(201); //allow, because already have LibraryExpressions
    }

    /**
     * add DynamicAdSlot failed by LibraryAdSlot DefaultLibraryAdSlot not existed
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryAdSlotInvalidByDefaultLibraryAdSlotNotExisted(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions defaultLibraryAdSlot' not existed
        $jsonData['libraryAdSlot']['defaultLibraryAdSlot'] = -1;

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by LibraryAdSlot DefaultLibraryAdSlot wrong dataType
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithLibraryAdSlotInvalidByDefaultLibraryAdSlotWrongDataType(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions defaultLibraryAdSlot' wrong data type
        $jsonData['libraryAdSlot']['defaultLibraryAdSlot'] = '3_wrong';

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by libraryAdSlot contains unexpected field
     * @param ApiTester $I
     */
    public function addDynamicAdSlotFailedByLibraryAdSlotContainsUnexpectedField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot' contains unexpected field
        $jsonData['libraryAdSlot']['unexpected_field'] = 'value'; //unexpected field

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by contains unexpected field
     * @param ApiTester $I
     */
    public function addDynamicAdSlotFailedByUnexpectedField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //contains unexpected field
        $jsonData['unexpected_field'] = 'value'; //unexpected field

        $I->sendPOST(URL_API . '/dynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit dynamicAdSlot
     * @depends addDynamicAdSlot
     * @param ApiTester $I
     */
    public function editDynamicAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expectAdSlot' null
        $jsonData['libraryAdSlot']['name'] = 'dynamic3-rename';

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(204);
    }

    /**
     * add DynamicAdSlot failed by Site not existed
     * @param ApiTester $I
     */
    public function editDynamicAdSlotFailedBySiteNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'site' not existed
        $jsonData['site'] = -1;

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by LibraryAdSlot is not json_array
     * @param ApiTester $I
     */
    public function editDynamicAdSlotFailedByLibraryAdSlotNotJsonArray(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot' this is not json_array
        $jsonData['libraryAdSlot'] = 'libraryAdSlot_not_json_array';

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by DefaultAdSlot not existed
     * @param ApiTester $I
     */
    public function editDynamicAdSlotFailedByDefaultAdSlotNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'DefaultAdSlot' not existed
        $jsonData['defaultAdSlot'] = -1;
        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by defaultAdSlot wrong dataType
     * @param ApiTester $I
     */
    public function editDynamicAdSlotFailedByWrongDataType(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'DefaultAdSlot' wrong data type
        $jsonData['defaultAdSlot'] = '3_wrong';

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by expressions is not json_array
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithLibraryExpressionNotJsonArray(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions' not json_array
        $jsonData['libraryAdSlot']['libraryExpressions'] = 'libraryExpressions_not_json_array';

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by expressions missing descriptor
     * @param ApiTester $I
     */
    //public function editDynamicAdSlotWithLibraryExpressionMissingDescriptor(ApiTester $I)
    //{
    //    $I->sendGet(URL_API . '/dynamicadslots');
    //    $item = array_pop($I->grabDataFromJsonResponse());
    //
    //    $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;
    //
    //    //'libraryAdSlot libraryExpressions expressionDescriptor' null
    //    unset($jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']);
    //
    //    $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
    //    $I->seeResponseCodeIs(400);
    //}

    /**
     *
     * add dynamicAdSlot failed caused by descriptor not json_array
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithLibraryExpressionDescriptorNotJsonArray(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor' not json_data
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor'] = 'expressionDescriptor_not_json_data';

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupType not supported
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithLibraryExpressionDescriptorGroupTypeNotSupported(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupType' null
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupType'] = 'NOT_SUPPORTED';

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupVal not json_array
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithLibraryExpressionDescriptorGroupValNotJsonArray(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal' not json_array
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'] = 'groupVal_not_json_array';

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupVal Var invalid
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithLibraryExpressionDescriptorGroupValVarInvalid(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal var' invalid
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['var'] = '~!@#$%^&*()_INVALID';

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupVal Cmp not supported
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithLibraryExpressionDescriptorGroupValCmpNotSupported(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal cmp' not supported
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['cmp'] = 'NOT_SUPPORTED';

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupVal type not supported
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithLibraryExpressionDescriptorGroupValTypeNotSupported(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal type' not supported
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['type'] = 'NOT_SUPPORTED';

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupVal type and val incompatible
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithLibraryExpressionDescriptorGroupValTypeIncompatible(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal' type and val incompatible
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['type'] = 'numeric';
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['val'] = '123_string';

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor groupVal contains unexpected field
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithLibraryExpressionDescriptorGroupValContainsUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal' contains unexpected field
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['unexpected_field'] = 'value';

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add dynamicAdSlot failed caused by descriptor contains unexpected field
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithLibraryExpressionDescriptorContainsUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expressionDescriptor groupVal' contains unexpected field
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressionDescriptor']['unexpected_field'] = 'value';

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by libraryExpressions expressions not json_array
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithLibraryExpressionInvalidByExpressionsNotJsonArray(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expectAdSlot' null
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressions'] = 'expressions_not_json_array';

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by libraryExpressions Expressions expectAdSlot not existed
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithLibraryExpressionInvalidByExpressionsExpectAdSlotNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions expectAdSlot' not existed
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressions']['expectAdSlot'] = -1;

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by InCompatible Native AdSlot for Expressions expectAdSlot
     * @param ApiTester $I
     */
    public function editDynamicAdSlotFailedByInCompatibleNativeAdSlotForExpressionsExpectAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions' native and expectAdSlot incompatible
        //$jsonData['libraryAdSlot']['native'] = false; //make sure supported native, already false
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['expressions']['expectAdSlot'] = PARAMS_NATIVE_AD_SLOT; //native ad slot

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by InCompatible Native AdSlot for defaultAdSlot
     * @param ApiTester $I
     */
    public function editDynamicAdSlotFailedByInCompatibleNativeAdSlotForDefaultAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions' native and defaultAdSlot incompatible
        //$jsonData['libraryAdSlot']['native'] = false; //make sure supported native, already false
        $jsonData['defaultAdSlot'] = PARAMS_NATIVE_AD_SLOT; //native ad slot

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by libraryAdSlot Expression's element contains unexpected field
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithLibraryExpressionInvalidByElementContainsUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryExpressions[0]' contains unexpected field
        $jsonData['libraryAdSlot']['libraryExpressions'][0]['unexpected_field'] = 'value'; //unexpected field

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by LibraryAdSlot DefaultLibraryAdSlot not existed
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithLibraryAdSlotInvalidByDefaultLibraryAdSlotNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions defaultLibraryAdSlot' not existed
        $jsonData['libraryAdSlot']['defaultLibraryAdSlot'] = -1;

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by LibraryAdSlot DefaultLibraryAdSlot wrong dataType
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithLibraryAdSlotInvalidByDefaultLibraryAdSlotWrongDataType(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot libraryExpressions defaultLibraryAdSlot' wrong data type
        $jsonData['libraryAdSlot']['defaultLibraryAdSlot'] = '3_wrong';

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by libraryAdSlot contains unexpected field
     * @param ApiTester $I
     */
    public function editDynamicAdSlotFailedByLibraryAdSlotContainsUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot' contains unexpected field
        $jsonData['libraryAdSlot']['unexpected_field'] = 'value'; //unexpected field

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by contains unexpected field
     * @param ApiTester $I
     */
    public function editDynamicAdSlotFailedByUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //contains unexpected field
        $jsonData['unexpected_field'] = 'value'; //unexpected field

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch dynamicAdSlot
     * @depends addDynamicAdSlot
     * @param ApiTester $I
     */
    public function patchDynamicAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'site' not change
        unset($jsonData['site']);
        //'defaultLibraryAdSlot' not change
        unset($jsonData['defaultAdSlot']);
        //'libraryAdSlot name' changed
        $jsonData['libraryAdSlot']['name'] = 'dtag.test.adslot-patched';

        $I->sendPATCH(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(204);
    }

    /**
     * patch dynamicAdSlot
     * @depends addDynamicAdSlot
     * @param ApiTester $I
     */
    public function patchDynamicAdSlotMoveToLibrary(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_DYNAMIC_AD_SLOT;

        //'libraryAdSlot name' changed
        $jsonData['libraryAdSlot']['visible'] = true;

        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/dynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete dynamicAdSlot
     * @depends addDynamicAdSlot
     */
    public function deleteDynamicAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_API . '/dynamicadslots/' . $item['id']);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete AdSlot Not Existed
     * @depends addDynamicAdSlot
     */
    public function deleteDynamicAdSlotNotExisted(ApiTester $I)
    {
        $I->sendDELETE(URL_API . '/dynamicadslots/' . '-1');
        $I->seeResponseCodeIs(404);
    }
}