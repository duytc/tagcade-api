<?php

class DynamicAdSlot
{
    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());
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
        $I->sendPOST(URL_API . '/dynamicadslots',
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'expressions' => [
                    [
                        'expressionDescriptor' => [
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
                        ],
                        'expectAdSlot' => PARAMS_EXPECTED_AD_SLOT,
                        'startingPosition' => 0
                    ],
                    [
                        'expressionDescriptor' => [
                            'var' => 'checkString',
                            'cmp' => '!=',
                            'val' => 'abc',
                            'type' => 'string'
                        ],
                        'expectAdSlot' => PARAMS_EXPECTED_AD_SLOT_2,
                        'startingPosition' => 1
                    ]
                ],
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT
            ]
        );
        $I->seeResponseCodeIs(201);
    }

    /**
     * add DynamicAdSlot failed by Missing Field
     * @param ApiTester $I
     */
    public function addDynamicAdSlotFailedByMissingField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/dynamicadslots',
            [
                'site' => PARAMS_SITE,
                //'name' => 'dtag.test.adslot', //this is missing field
                'expressions' => [
                    [
                        'expressionDescriptor' => [
                            'var' => 'checkString',
                            'cmp' => '!=',
                            'val' => 'abc',
                            'type' => 'string'
                        ],
                        'expectAdSlot' => PARAMS_EXPECTED_AD_SLOT,
                        'startingPosition' => 0
                    ]
                ],
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by Unexpected Field
     * @param ApiTester $I
     */
    public function addDynamicAdSlotFailedByUnexpectedField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/dynamicadslots',
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'expressions' => [
                    [
                        'expressionDescriptor' => [
                            'var' => 'checkString',
                            'cmp' => '!=',
                            'val' => 'abc',
                            'type' => 'string'
                        ],
                        'expectAdSlot' => PARAMS_EXPECTED_AD_SLOT,
                        'startingPosition' => 0
                    ]
                ],
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT,
                'unexpected_field' => 29 //this is unexpected field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add DynamicAdSlot failed by Unexpected Field
     * @param ApiTester $I
     */
    public function addDynamicAdSlotFailedByWrongDataType(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/dynamicadslots',
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'expressions' => [
                    [
                        'expressionDescriptor' => [
                            'var' => 'checkString',
                            'cmp' => '!=',
                            'val' => 'abc',
                            'type' => 'string'
                        ],
                        'expectAdSlot' => PARAMS_EXPECTED_AD_SLOT,
                        'startingPosition' => 0
                    ]
                ],
                'defaultAdSlot' => '29_wrong' //this is wrong data type
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by expressions null
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithExpressionsNull(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/dynamicadslots',
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'expressions' => null,
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT
            ]
        );
        $I->seeResponseCodeIs(201);
    }

    /**
     *
     * add dynamicAdSlot failed caused by expressions format invalid
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithExpressionsInvalid(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/dynamicadslots',
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'expressions' => [
                    [
                        'expressionDescriptor_wrong' => [ //this key is invalid
                            'var' => 'checkString',
                            'cmp' => '!=',
                            'val' => 'abc',
                            'type' => 'string'
                        ],
                        'expectAdSlot' => PARAMS_EXPECTED_AD_SLOT,
                        'startingPosition' => 0
                    ]
                ],
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by expressions invalid at adSlot not existed
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithExpressionsInvalidByAdSlotNotExisted(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/dynamicadslots',
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'expressions' => [
                    [
                        'expressionDescriptor' => [
                            'var' => 'checkString',
                            'cmp' => '!=',
                            'val' => 'abc',
                            'type' => 'string'
                        ],
                        'expectAdSlot' => -1, //this adSlot is not existed
                        'startingPosition' => 0
                    ]
                ],
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT
            ]
        );
        $I->seeResponseCodeIs(400);
    }

//    /**
//     * add DynamicAdSlot failed by UnCompatible Native AdSlot
//     * @param ApiTester $I
//     */
//    public function addDynamicAdSlotFailedByUnCompatibleNativeAdSlot(ApiTester $I)
//    {
//        $I->sendPOST(URL_API . '/dynamicadslots',
//            [
//                'site' => PARAMS_SITE,
//                'name' => 'dtag.test.adslot',
//                'native' => false, //not support native
//                'expressions' => [
//                    [
//                        'expressionDescriptor' => [
//                            'var' => 'checkString',
//                            'cmp' => '!=',
//                            'val' => 'abc',
//                            'type' => 'string'
//                        ],
//                        'expectAdSlot' => PARAMS_NATIVE_AD_SLOT, //this is not supported
//                        'startingPosition' => 0
//                    ]
//                ],
//                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT
//            ]
//        );
//        $I->seeResponseCodeIs(400);
//    }

    /**
     * edit dynamicAdSlot
     * @depends addDynamicAdSlot
     * @param ApiTester $I
     */
    public function editDynamicAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'expressions' => [
                    [
                        'expressionDescriptor' => [
                            'groupType' => 'AND',
                            'groupVal' => [
                                [
                                    'var' => 'checkLength',
                                    'cmp' => 'length >=',
                                    'val' => 10,
                                    'type' => 'numeric'
                                ],
                                [
                                    'var' => 'checkString',
                                    'cmp' => '!=',
                                    'val' => 'abc',
                                    'type' => 'string'
                                ]
                            ]
                        ],
                        'expectAdSlot' => PARAMS_EXPECTED_AD_SLOT,
                        'startingPosition' => 0
                    ],
                    [
                        'expressionDescriptor' => [
                            'var' => 'checkString',
                            'cmp' => '!=',
                            'val' => 'abc',
                            'type' => 'string'
                        ],
                        'expectAdSlot' => PARAMS_EXPECTED_AD_SLOT_2,
                        'startingPosition' => 1
                    ]
                ],
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT
            ]
        );
        $I->seeResponseCodeIs(204);
    }

    /**
     * edit dynamicAdSlot failed by unexpected field
     * @depends addDynamicAdSlot
     * @param ApiTester $I
     */
    public function editDynamicAdSlotFailedByUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'expressions' => [
                    [
                        'expressionDescriptor' => [
                            'var' => 'checkString',
                            'cmp' => '!=',
                            'val' => 'abc',
                            'type' => 'string'
                        ],
                        'expectAdSlot' => PARAMS_EXPECTED_AD_SLOT,
                        'startingPosition' => 0
                    ]
                ],
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT,
                'unexpected_field' => 29 //this is unexpected field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit dynamicAdSlot failed by wrong data type
     * @depends addDynamicAdSlot
     * @param ApiTester $I
     */
    public function editDynamicAdSlotFailedByWrongDataType(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'expressions' => [
                    [
                        'expressionDescriptor' => [
                            'var' => 'checkString',
                            'cmp' => '!=',
                            'val' => 'abc',
                            'type' => 'string'
                        ],
                        'expectAdSlot' => PARAMS_EXPECTED_AD_SLOT,
                        'startingPosition' => 0
                    ]
                ],
                'defaultAdSlot' => '29_wrong' //this is wrong data type
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit dynamicAdSlot failed caused by expressions null
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithExpressionsNull(ApiTester $I)
    {

        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'defaultAdSlot' => null,
                'expressions' => null
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit dynamicAdSlot failed caused by variableDescriptor format wrong
     * @depends addDynamicAdSlot
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithExpressionsInvalid(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'expressions' => [
                    [
                        'expressionDescriptor_wrong' => [ //this key is invalid
                            'var' => 'checkString',
                            'cmp' => '!=',
                            'val' => 'abc',
                            'type' => 'string'
                        ],
                        'expectAdSlot' => PARAMS_EXPECTED_AD_SLOT,
                        'startingPosition' => 0
                    ]
                ],
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit dynamicAdSlot failed caused by variableDescriptor format wrong
     * @depends addDynamicAdSlot
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithExpressionsInvalidByAdSlotNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'expressions' => [
                    [
                        'expressionDescriptor_wrong' => [
                            'var' => 'checkString',
                            'cmp' => '!=',
                            'val' => 'abc',
                            'type' => 'string'
                        ],
                        'expectAdSlot' => -1, //this adSlot is not existed
                        'startingPosition' => 0
                    ]
                ],
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit dynamicAdSlot failed by UnCompatible Native AdSlot
     * @depends addDynamicAdSlot
     * @param ApiTester $I
     */
    public function editDynamicAdSlotFailedByUnCompatibleNativeAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'native' => false, //not support native
                'expressions' => [
                    [
                        'expressionDescriptor' => [
                            'var' => 'checkString',
                            'cmp' => '!=',
                            'val' => 'abc',
                            'type' => 'string'
                        ],
                        'expectAdSlot' => PARAMS_NATIVE_AD_SLOT, //this is not supported
                        'startingPosition' => 0
                    ]
                ],
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT
            ]
        );
        $I->seeResponseCodeIs(400);
    }

//    /**
//     * patch dynamicAdSlot
//     * @depends addDynamicAdSlot
//     * @param ApiTester $I
//     */
//    public function patchDynamicAdSlot(ApiTester $I)
//    {
//        //TODO - case failed
//        $I->sendGet(URL_API . '/dynamicadslots');
//        $item = array_pop($I->grabDataFromJsonResponse());
//
//        $I->sendPATCH(URL_API . '/dynamicadslots/' . $item['id'],
//            [
//                'name' => 'dtag.test.adslot.patched'
//            ]
//        );
//        $I->seeResponseCodeIs(204);
//    }

    /**
     * patch dynamicAdSlot failed cause by unexpected field
     * @depends addDynamicAdSlot
     * @param ApiTester $I
     */
    public function patchDynamicAdSlotFailedByUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/dynamicadslots/' . $item['id'],
            [
                'name' => 'dtag.test.adslot.patched',
                'unexpected_field' => 29 //this is unexpected field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

//    /**
//     * patch dynamicAdSlot failed caused by expressions null
//     * @depends addDynamicAdSlot
//     * @param ApiTester $I
//     */
//    public function patchDynamicAdSlotWithExpressionsNull(ApiTester $I)
//    {
//        //TODO - case failed
//        $I->sendGet(URL_API . '/dynamicadslots');
//        $item = array_pop($I->grabDataFromJsonResponse());
//
//        $I->sendPATCH(URL_API . '/dynamicadslots/' . $item['id'],
//            [
//                'variableDescriptor' => null
//            ]
//        );
//        $I->seeResponseCodeIs(400);
//    }

//    /**
//     * patch dynamicAdSlot failed caused by expressions format wrong
//     * @depends addDynamicAdSlot
//     * @param ApiTester $I
//     */
//    public function patchDynamicAdSlotWithExpressionsInvalid(ApiTester $I)
//    {
//        //TODO - case failed
//        $I->sendGet(URL_API . '/dynamicadslots');
//        $item = array_pop($I->grabDataFromJsonResponse());
//
//        $I->sendPATCH(URL_API . '/dynamicadslots/' . $item['id'],
//            [
//                'expressions' => [
//                    [
//                        'expressionDescriptor_wrong' => [ //this key is invalid
//                            'var' => 'checkString',
//                            'cmp' => '!=',
//                            'val' => 'abc',
//                            'type' => 'string'
//                        ],
//                        'expectAdSlot' => PARAMS_EXPECTED_AD_SLOT
//                    ]
//                ],
//                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT
//            ]
//        );
//        $I->seeResponseCodeIs(400);
//    }

    /**
     * patch dynamicAdSlot failed caused by expressions invalid at adSlot not existed
     * @depends addDynamicAdSlot
     * @param ApiTester $I
     */
    public function patchDynamicAdSlotWithExpressionsInvalidByAdSlotNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/dynamicadslots/' . $item['id'],
            [
                'expressions' => [
                    [
                        'expressionDescriptor_wrong' => [ //this key is invalid
                            'var' => 'checkString',
                            'cmp' => '!=',
                            'val' => 'abc',
                            'type' => 'string'
                        ],
                        'expectAdSlot' => -1, //this is not existed
                        'startingPosition' => 0
                    ]
                ],
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch dynamicAdSlot failed caused by UnCompatible Native AdSlot
     * @depends addDynamicAdSlot
     * @param ApiTester $I
     */
    public function patchDynamicAdSlotFailedByUnCompatibleNativeAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/dynamicadslots/' . $item['id'],
            [
                'native' => false, //not support native
                'expressions' => [
                    [
                        'expressionDescriptor_wrong' => [ //this key is invalid
                            'var' => 'checkString',
                            'cmp' => '!=',
                            'val' => 'abc',
                            'type' => 'string'
                        ],
                        'expectAdSlot' => PARAMS_NATIVE_AD_SLOT, //this is not supported
                        'startingPosition' => 0
                    ]
                ],
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT
            ]
        );
        $I->seeResponseCodeIs(400);
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