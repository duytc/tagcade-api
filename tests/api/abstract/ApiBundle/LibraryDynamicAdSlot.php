<?php

class LibraryDynamicAdSlot
{
    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());
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
        $I->sendPOST(URL_API . '/librarydynamicadslots',
            [
                //'publisher' => 2,
                //'visible' => true,
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
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT,
                'name' => 'dtag.test.adslot'
            ]
        );
        $I->seeResponseCodeIs(201);
    }

    /**
     * add LibraryDynamicAdSlot failed by Missing Field
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotFailedByMissingField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/librarydynamicadslots',
            [
                //'publisher' => 2,
                //'visible' => true,
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
                //'name' => 'dtag.test.adslot' //this is missing field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryDynamicAdSlot failed by Unexpected Field
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotFailedByUnexpectedField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/librarydynamicadslots',
            [
                //'publisher' => 2,
                //'visible' => true,
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
                'name' => 'dtag.test.adslot',
                'unexpected_field' => 29 //this is unexpected field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryDynamicAdSlot failed by Unexpected Field
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotFailedByWrongDataType(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/librarydynamicadslots',
            [
                //'publisher' => 2,
                //'visible' => true,
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
                'defaultAdSlot' => '29_wrong', //this is wrong data type
                'name' => 'dtag.test.adslot'
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add libraryDynamicAdSlot failed caused by expressions null
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithExpressionsNull(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/librarydynamicadslots',
            [
                //'publisher' => 2,
                //'visible' => true,
                'expressions' => null,
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT,
                'name' => 'dtag.test.adslot'
            ]
        );
        //$I->seeResponseCodeIs(201);
        $I->seeResponseCodeIs(400); //now not allow null
    }

    /**
     *
     * add libraryDynamicAdSlot failed caused by expressions format invalid
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithExpressionsInvalid(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/librarydynamicadslots',
            [
                //'publisher' => 2,
                //'visible' => true,
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
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT,
                'name' => 'dtag.test.adslot'
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add libraryDynamicAdSlot failed caused by expressions invalid at adSlot not existed
     * @param ApiTester $I
     */
    public function addLibraryDynamicAdSlotWithExpressionsInvalidByAdSlotNotExisted(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/librarydynamicadslots',
            [
                //'publisher' => 2,
                //'visible' => true,
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
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT,
                'name' => 'dtag.test.adslot'
            ]
        );
        $I->seeResponseCodeIs(400);
    }

//    /**
//     * add LibraryDynamicAdSlot failed by UnCompatible Native AdSlot
//     * @param ApiTester $I
//     */
//    public function addLibraryDynamicAdSlotFailedByUnCompatibleNativeAdSlot(ApiTester $I)
//    {
//        $I->sendPOST(URL_API . '/librarydynamicadslots',
//            [
//                'native' => false, //not support native
//                    'expressions' => [
//                        [
//                            'expressionDescriptor' => [
//                                'var' => 'checkString',
//                                'cmp' => '!=',
//                                'val' => 'abc',
//                                'type' => 'string'
//                            ],
//                            'expectAdSlot' => PARAMS_NATIVE_AD_SLOT, //this is not supported
//                            'startingPosition' => 0
//                        ]
//                    ],
//                    'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT,
//                    'name' => 'dtag.test.adslot'
//                ]
//            ]
//        );
//        $I->seeResponseCodeIs(400);
//    }

    /**
     * patch libraryDynamicAdSlot
     * @depends addLibraryDynamicAdSlot
     * @param ApiTester $I
     */
    public function patchLibraryDynamicAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'],
            [
                'name' => 'dtag.test.adslot-patched'
            ]
        );
        $I->seeResponseCodeIs(204);
    }

    /**
     * patch libraryDynamicAdSlot set visible=false
     * @depends addLibraryDynamicAdSlot
     * @param ApiTester $I
     */
    public function patchLibraryDynamicAdSlotSetInvisible(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'],
            [
                'name' => 'dtag.test.adslot-lib',
                'visible' => false
            ]
        );
        //$I->seeResponseCodeIs(400); //not allow
        $I->canSeeResponseCodeIs(204); //will be delete this adSlot if has no reference
        //$I->canSeeResponseCodeIs(400); //will be error if has at least one reference
    }

    /**
     * patch libraryDynamicAdSlot failed cause by unexpected field
     * @depends addLibraryDynamicAdSlot
     * @param ApiTester $I
     */
    public function patchLibraryDynamicAdSlotFailedByUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'],
            [
                'name' => 'dtag.test.adslot-lib',
                'unexpected_field' => 29 //this is unexpected field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

//    /**
//     * patch libraryDynamicAdSlot failed caused by expressions null
//     * @depends addLibraryDynamicAdSlot
//     * @param ApiTester $I
//     */
//    public function patchLibraryDynamicAdSlotWithExpressionsNull(ApiTester $I)
//    {
//        //TODO - case failed
//        $I->sendGet(URL_API . '/librarydynamicadslots');
//        $item = array_pop($I->grabDataFromJsonResponse());
//
//        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'],
//            [
//                'variableDescriptor' => null
//            ]
//        );
//        $I->seeResponseCodeIs(400);
//    }

//    /**
//     * patch libraryDynamicAdSlot failed caused by expressions format wrong
//     * @depends addLibraryDynamicAdSlot
//     * @param ApiTester $I
//     */
//    public function patchLibraryDynamicAdSlotWithExpressionsInvalid(ApiTester $I)
//    {
//        //TODO - case failed
//        $I->sendGet(URL_API . '/librarydynamicadslots');
//        $item = array_pop($I->grabDataFromJsonResponse());
//
//        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'],
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
     * patch libraryDynamicAdSlot failed caused by expressions invalid at adSlot not existed
     * @depends addLibraryDynamicAdSlot
     * @param ApiTester $I
     */
    public function patchLibraryDynamicAdSlotWithExpressionsInvalidByAdSlotNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'],
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
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT,
                'name' => 'dtag.test.adslot',
                'visible' => true
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch libraryDynamicAdSlot failed caused by UnCompatible Native AdSlot
     * @depends addLibraryDynamicAdSlot
     * @param ApiTester $I
     */
    public function patchLibraryDynamicAdSlotFailedByUnCompatibleNativeAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'],
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
                'defaultAdSlot' => PARAMS_DEFAULT_AD_SLOT,
                'name' => 'dtag.test.adslot',
                'visible' => true
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * delete libraryDynamicAdSlot
     * @depends addLibraryDynamicAdSlot
     */
    public function deleteLibraryDynamicAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_API . '/librarydynamicadslots/' . $item['id']);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete AdSlot Not Existed
     * @depends addLibraryDynamicAdSlot
     */
    public function deleteLibraryDynamicAdSlotNotExisted(ApiTester $I)
    {
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