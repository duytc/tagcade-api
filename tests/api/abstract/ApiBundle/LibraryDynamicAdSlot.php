<?php

class LibraryDynamicAdSlot
{
    static $JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT = [];

    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());

        self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT = [
            //'publisher' => 2,
            //'visible' => true, //default true
            //'native' => false, //default false
            'name' => 'dynamicAdSlot-test',
            'libraryExpressions' => [
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
                    'startingPosition' => 1
                ]
            ],
            'defaultLibraryAdSlot' => PARAMS_LIBRARY_AD_SLOT
        ];
    }

    public function _after(ApiTester $I)
    {
    }

    /**
     * get All LibraryDynamicAdSlot
     * @param ApiTester $I
     */
    public function getAllLibraryDynamicAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarydynamicadslots');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get LibraryDynamicAdSlot By Id
     * @param ApiTester $I
     */
    public function getLibraryDynamicAdSlotById(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarydynamicadslots/' . PARAMS_LIBRARY_DYNAMIC_AD_SLOT);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get LibraryDynamicAdSlot By Id Not Existed
     * @param ApiTester $I
     */
    public function getLibraryDynamicAdSlotByIdNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarydynamicadslots/' . '-1');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }

    /**
     * add LibraryDynamicAdSlot
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlot(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        // full expressionDescriptor
        $jsonData['libraryExpressions'][0]['expressionDescriptor'] = [
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

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(201);
    }

    /**
     * add LibraryDynamicAdSlot failed by Missing name
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotFailedByMissingName(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        // 'name' missing
        unset($jsonData['name']);

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryDynamicAdSlot failed by name null
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotFailedByNameNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        // 'name' missing
        $jsonData['name'] = null;

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryDynamicAdSlot failed caused by expressions null only when have defaultLibraryAdSlot
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions' null
        $jsonData['libraryExpressions'] = null;

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(201); //allow if has defaultLibraryAdSlot
        //$I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryDynamicAdSlot failed caused by expressions is not json_array
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsNotJsonArray(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions' not json_array
        $jsonData['libraryExpressions'] = 'libraryExpressions_not_json_array';

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by expressions missing descriptor
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsMissingDescriptor(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor' missing
        unset($jsonData['libraryExpressions'][0]['expressionDescriptor']);

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by expressions descriptor null
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsDescriptorNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor' null
        $jsonData['libraryExpressions'][0]['expressionDescriptor'] = null;

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by descriptor not json_array
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsDescriptorNotJsonArray(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor' not json_data
        $jsonData['libraryExpressions'][0]['expressionDescriptor'] = 'expressionDescriptor_not_json_data';

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by descriptor missing groupType
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsDescriptorMissingGroupType(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor groupType' missing
        unset($jsonData['libraryExpressions'][0]['expressionDescriptor']['groupType']);

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by descriptor groupType null
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsDescriptorGroupTypeNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor groupType' null
        $jsonData['libraryExpressions'][0]['expressionDescriptor']['groupType'] = null;

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by descriptor groupType not supported
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsDescriptorGroupTypeNotSupported(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor groupType' null
        $jsonData['libraryExpressions'][0]['expressionDescriptor']['groupType'] = 'NOT_SUPPORTED';

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by descriptor missing groupVal
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsDescriptorMissingGroupVal(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor groupVal' missing
        unset($jsonData['libraryExpressions'][0]['expressionDescriptor']['groupVal']);

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by descriptor groupVal null
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsDescriptorGroupValNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor groupVal' null
        $jsonData['libraryExpressions'][0]['expressionDescriptor']['groupVal'] = null;

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by descriptor groupVal not json_array
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsDescriptorGroupValNotJsonArray(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor groupVal' not json_array
        $jsonData['libraryExpressions'][0]['expressionDescriptor']['groupVal'] = 'groupVal_not_json_array';

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by descriptor groupVal Var missing
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsDescriptorGroupValMissingVar(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor groupVal var' missing
        unset($jsonData['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['var']);

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by descriptor groupVal Var invalid
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsDescriptorGroupValVarInvalid(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor groupVal var' invalid
        $jsonData['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['var'] = '~!@#$%^&*()_INVALID';

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by descriptor groupVal Cmp missing
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsDescriptorGroupValMissingCmp(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor groupVal cmp' missing
        unset($jsonData['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['cmp']);

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by descriptor groupVal Cmp not supported
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsDescriptorGroupValCmpNotSupported(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor groupVal cmp' not supported
        $jsonData['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['cmp'] = 'NOT_SUPPORTED';

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by descriptor groupVal Val missing
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsDescriptorGroupValMissingVal(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor groupVal val' missing
        unset($jsonData['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['val']);

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by descriptor groupVal Type missing
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsDescriptorGroupValMissingType(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor groupVal type' missing
        unset($jsonData['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['type']);

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by descriptor groupVal type not supported
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsDescriptorGroupValTypeNotSupported(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor groupVal type' not supported
        $jsonData['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['type'] = 'NOT_SUPPORTED';

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by descriptor groupVal type and val incompatible
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsDescriptorGroupValTypeIncompatible(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor groupVal' type and val incompatible
        $jsonData['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['type'] = 'numeric';
        $jsonData['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['val'] = '123_string';

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by descriptor groupVal contains unexpected field
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsDescriptorGroupValContainsUnexpectedField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor groupVal' contains unexpected field
        $jsonData['libraryExpressions'][0]['expressionDescriptor']['groupVal'][0]['unexpected_field'] = 'value';

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     *
     * add LibraryDynamicAdSlot failed caused by descriptor contains unexpected field
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsDescriptorContainsUnexpectedField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions expressionDescriptor groupVal' contains unexpected field
        $jsonData['libraryExpressions'][0]['expressionDescriptor']['unexpected_field'] = 'value';

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryDynamicAdSlot failed caused by expressions missing startingPosition
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsInvalidByMissingStartingPosition(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions startingPosition' missing
        unset($jsonData['libraryExpressions'][0]['startingPosition']);

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(201); //api auto correct to '1'
        //$I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryDynamicAdSlot failed caused by expressions startingPosition null
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsInvalidByStartingPositionNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions startingPosition' null
        $jsonData['libraryExpressions'][0]['startingPosition'] = null;

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(201); //api auto correct to '1'
        //$I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryDynamicAdSlot failed caused by expressions invalid at adSlot not existed
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithLibraryExpressionsInvalidByStartingPositionInvalid(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions startingPosition' invalid
        $jsonData['libraryExpressions'][0]['startingPosition'] = -1;

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(201); //api auto correct to '1'
        //$I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by InCompatible Native AdSlot for expectLibraryAdSlot
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotFailedByInCompatibleNativeAdSlotForExpectLibraryAdSlot(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions' native and expectAdSlot incompatible
        //$jsonData['native'] = false; //make sure not supported native
        $jsonData['libraryExpressions'][0]['expectLibraryAdSlot'] = PARAMS_LIBRARY_NATIVE_AD_SLOT; //library native ad slot

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by InCompatible Native AdSlot for defaultAdSlot
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotFailedByInCompatibleNativeAdSlotForDefaultAdSlot(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions' native and defaultAdSlot incompatible
        //$jsonData['native'] = false; //make sure not supported native
        $jsonData['defaultLibraryAdSlot'] = PARAMS_LIBRARY_NATIVE_AD_SLOT; //library native ad slot

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by contains unexpected field
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotFailedByUnexpectedField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //contains unexpected field
        $jsonData['unexpected_field'] = 'value'; //unexpected field

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by Missing DefaultLibraryAdSlot only when have libraryExpressions
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotFailedByMissingDefaultLibraryAdSlot(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'defaultLibraryAdSlot' missing
        unset($jsonData['defaultLibraryAdSlot']);

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        //$I->seeResponseCodeIs(400);
        $I->seeResponseCodeIs(201); //allow, because already have libraryExpressiosn
    }

    /**
     * add DynamicAdSlot failed by DefaultLibraryAdSlot null only when have defaultLibraryAdSlot
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotFailedByDefaultLibraryAdSlotNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'defaultLibraryAdSlot' null
        $jsonData['defaultLibraryAdSlot'] = null;

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        //$I->seeResponseCodeIs(400);
        $I->seeResponseCodeIs(201); //allow, because already have libraryExpressiosn
    }

    /**
     * add DynamicAdSlot failed by both libraryExpressions and DefaultLibraryAdSlot null
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotFailedByBothLibraryExpressionsAndDefaultLibraryAdSlotNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //both 'libraryExpressions' and 'defaultLibraryAdSlot' null
        $jsonData['libraryExpressions'] = null;
        $jsonData['defaultLibraryAdSlot'] = null;

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400); //not allow, because at least one of libraryExpressions and DefaultAdSlot not null
    }

    /**
     * add DynamicAdSlot failed by DefaultLibraryAdSlot not existed
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotFailedByDefaultLibraryAdSlotNotExisted(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'defaultLibraryAdSlot' not existed
        $jsonData['defaultLibraryAdSlot'] = -1;
        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by Unexpected Field
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotFailedByWrongDataType(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'defaultLibraryAdSlot' wrong data type
        $jsonData['defaultLibraryAdSlot'] = '3_wrong';

        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch libraryDynamicAdSlot
     * @param ApiTester $I
     */
    public function patchLibraryDynamicAdSlot(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryDynamicAdSlot($I);

        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions, defaultLibraryAdSlot' not set
        unset($jsonData['libraryExpressions']);
        unset($jsonData['defaultLibraryAdSlot']);
        //'name' changed
        $jsonData['name'] = 'dtag.test.adslot-patched';

        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(204);
    }

    /**
     * patch libraryDynamicAdSlot set visible=false
     * @param ApiTester $I
     */
    public function patchLibraryDynamicAdSlotSetInvisible(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryDynamicAdSlot($I);

        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions, defaultLibraryAdSlot' not set
        unset($jsonData['libraryExpressions']);
        unset($jsonData['defaultLibraryAdSlot']);
        //'name' changed
        $jsonData['name'] = 'dtag.test.adslot-patched';
        //'visible' changed
        $jsonData['visible'] = false;

        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'], $jsonData);
        //$I->seeResponseCodeIs(400); //not allow
        $I->canSeeResponseCodeIs(204); //will be delete this adSlot if has no reference
        //$I->canSeeResponseCodeIs(400); //will be error if has at least one reference
    }

    /**
     * patch libraryDynamicAdSlot failed cause by unexpected field
     * @param ApiTester $I
     */
    public function patchLibraryDynamicAdSlotFailedByUnexpectedField(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryDynamicAdSlot($I);

        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'libraryExpressions, defaultLibraryAdSlot' not set
        unset($jsonData['libraryExpressions']);
        unset($jsonData['defaultLibraryAdSlot']);
        //'name' changed
        $jsonData['name'] = 'dtag.test.adslot-patched';
        //'unexpected_field' added
        $jsonData['unexpected_field'] = 'value'; //this is unexpected field

        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch libraryDynamicAdSlot failed cause by LibraryExpression not json_array
     * @param ApiTester $I
     */
    public function patchLibraryDynamicAdSlotFailedByLibraryExpressNotJsonArray(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryDynamicAdSlot($I);

        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'name, defaultLibraryAdSlot' not set
        unset($jsonData['name']);
        unset($jsonData['defaultLibraryAdSlot']);
        //'libraryExpressions' not json_array
        $jsonData['libraryExpressions'] = 'libraryExpression_not_json_data';

        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch libraryDynamicAdSlot failed cause by LibraryExpression descriptor not json_array
     * @param ApiTester $I
     */
    public function patchLibraryDynamicAdSlotFailedByLibraryExpressDescriptorNotJsonArray(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryDynamicAdSlot($I);

        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'name, defaultLibraryAdSlot' not set
        unset($jsonData['name']);
        unset($jsonData['defaultLibraryAdSlot']);
        //'libraryExpressions expressionDescriptor' not json_data
        $jsonData['libraryExpressions'][0]['expressionDescriptor'] = 'expressionDescriptor_not_json_data';

        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch libraryDynamicAdSlot failed cause by LibraryExpression descriptor contains unexpected field
     * @param ApiTester $I
     */
    public function patchLibraryDynamicAdSlotFailedByLibraryExpressDescriptorContainsUnexpectedField(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryDynamicAdSlot($I);

        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'name, defaultLibraryAdSlot' not set
        unset($jsonData['name']);
        unset($jsonData['defaultLibraryAdSlot']);
        //'libraryExpressions expressionDescriptor' changed
        $jsonData['libraryExpressions'][0]['expressionDescriptor']['unexpected_field'] = 'value'; //this is unexpected field

        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch libraryDynamicAdSlot failed cause by expressions expectAdSlot not existed
     * @param ApiTester $I
     */
    public function patchLibraryDynamicAdSlotFailedByExpectLibraryAdSlotNotExisted(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryDynamicAdSlot($I);

        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'name, defaultLibraryAdSlot' not set
        unset($jsonData['name']);
        unset($jsonData['defaultLibraryAdSlot']);
        //'libraryExpressions expectAdSlot' not existed
        $jsonData['libraryExpressions'][0]['expectLibraryAdSlot'] = -1;

        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch libraryDynamicAdSlot failed cause by changing Native field
     * @param ApiTester $I
     */
    public function patchLibraryDynamicAdSlotFailedByChangingNative(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryDynamicAdSlot($I);

        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'name, defaultLibraryAdSlot, libraryExpressions' not set
        unset($jsonData['name']);
        unset($jsonData['defaultLibraryAdSlot']);
        unset($jsonData['libraryExpressions']);
        //'native' changed
        $jsonData['native'] = true; //change from false to true, BUT can not be changed in EDIT mode

        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch libraryDynamicAdSlot failed cause by DefaultLibraryAdSlot not existed
     * @param ApiTester $I
     */
    public function patchLibraryDynamicAdSlotFailedByDefaultLibraryAdSlotNotExisted(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryDynamicAdSlot($I);

        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'name, libraryExpressions' not set
        unset($jsonData['name']);
        unset($jsonData['libraryExpressions']);
        //'defaultLibraryAdSlot' not existed
        $jsonData['defaultLibraryAdSlot'] = -1;

        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch libraryDynamicAdSlot failed cause by Unexpected Field
     * @param ApiTester $I
     */
    public function patchLibraryDynamicAdSlotFailedByWrongDataType(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryDynamicAdSlot($I);

        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        //'name, libraryExpressions' not set
        unset($jsonData['name']);
        unset($jsonData['libraryExpressions']);
        //'defaultLibraryAdSlot' wrong data type
        $jsonData['defaultLibraryAdSlot'] = '3_wrong';

        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    public function patchLibraryDynamicAdSlotWithNewLibraryNativeAdSlot(ApiTester $I)
    {
        $jsonData =  [
            'name' => 'dtag.test.librarynativeadslot',
        ];

        $I->sendPOST(URL_API . '/librarynativeadslots', $jsonData);

        $I->seeResponseCodeIs(201);
        $I->sendGet(URL_API . '/librarynativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;
        unset($jsonData['name']);
        unset($jsonData['defaultLibraryAdSlot']);
        $jsonData['libraryExpressions'][0]['expectLibraryAdSlot'] = $item['id'];
        unset($jsonData['libraryExpressions'][0]['startingPosition']);

        //add new before editing
        $jsonDynamic = self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT;

        // full expressionDescriptor
        $jsonDynamic['libraryExpressions'][0]['expressionDescriptor'] = [
            'groupType' => 'AND',
            'groupVal' => [
                [
                    'var' => 'checkLength',
                    'cmp' => 'length >=',
                    'val' => 10,
                    'type' => 'numeric'
                ]
            ]
        ];
        $jsonDynamic['native'] = true;
        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonDynamic);
        $I->seeResponseCodeIs(201);

        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete libraryDynamicAdSlot
     */
    public function deleteLibraryDynamicAdSlot(ApiTester $I)
    {
        //add new before deleting
        $this->addLibraryDynamicAdSlot($I);

        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_API . '/librarydynamicadslots/' . $item['id']);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete AdSlot Not Existed
     */
    public function deleteLibraryDynamicAdSlotNotExisted(ApiTester $I)
    {
        //add new before deleting
        $this->addLibraryDynamicAdSlot($I);

        $I->sendDELETE(URL_API . '/librarydynamicadslots/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * Get those AdSlots which refer to the current AdSlot Library
     * @param ApiTester $I
     */
    public function getAssociatedadslotsAction(ApiTester $I)
    {
        $I->sendGET(URL_API . '/librarydynamicadslots/' . PARAMS_LIBRARY_DYNAMIC_AD_SLOT . '/associatedadslots');
        $I->seeResponseCodeIs(200);
    }

    /**
     * Get those AdSlots which refer to the current AdSlot Library
     * @param ApiTester $I
     */
    public function getAssociatedadslotsActionNotExisted(ApiTester $I)
    {
        $I->sendGET(URL_API . '/librarydynamicadslots/' . '-1' . '/associatedadslots');
        $I->seeResponseCodeIs(404);
    }
}