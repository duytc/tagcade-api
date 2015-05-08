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

    public function getAllDynamicAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function getDynamicAdSlotById(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots/' . PARAMS_AD_SLOT);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function addDynamicAdSlot(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/dynamicadslots',
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'enableVariable' => true,
                'variableDescriptor' => [
                    'expressions' => [
                        [
                            'expression' => [
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
                            'expectAdSlot' => 6
                        ]
                    ]
                ]
            ]
        );
        $I->seeResponseCodeIs(201);
    }

    /**
     * add dynamicAdSlot failed caused by variableDescriptor null
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithVariableDescriptorNull(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/dynamicadslots',
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'enableVariable' => true,
                'variableDescriptor' => [
                    'expressions' => null
                ]
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by variableDescriptor format wrong
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithVariableDescriptorInvalid(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/dynamicadslots',
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'enableVariable' => true,
                'variableDescriptor' => [
                    'expressions_wrong' => [
                        [
                            'expression' => [
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
                            'expectAdSlot' => 23
                        ]
                    ]
                ]
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by missing variableDescriptor field
     * @param ApiTester $I
     */
    public function addDynamicAdSlotMissingField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/dynamicadslots',
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'enableVariable' => true
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add dynamicAdSlot failed caused by contains unexpected field
     * @param ApiTester $I
     */
    public function addDynamicAdSlotWithUnexpectedField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/dynamicadslots',
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'enableVariable' => true,
                'variableDescriptor_unexpected' => 'variableDescriptor_unexpected',
                'variableDescriptor' => [
                    'expressions' => [
                        [
                            'expression' => [
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
                            'expectAdSlot' => 23
                        ]
                    ]
                ]
            ]
        );
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

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'variableDescriptor' => [
                    'expressions' => [
                        [
                            'expression' => [
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
                            'expectAdSlot' => 23
                        ]
                    ]
                ]
            ]
        );
        $I->seeResponseCodeIs(204);
    }

    /**
    /**
     * edit dynamicAdSlot failed caused by variableDescriptor null
     * @depends addDynamicAdSlot
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithVariableDescriptorNull(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'enableVariable' => true,
                'variableDescriptor' => [
                    'expressions' => null
                ]
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit dynamicAdSlot failed caused by variableDescriptor format wrong
     * @depends addDynamicAdSlot
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithVariableDescriptorInvalid(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'enableVariable' => true,
                'variableDescriptor' => [
                    'expressions_wrong' => [
                        [
                            'expression' => [
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
                            'expectAdSlot' => 23
                        ]
                    ]
                ]
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit dynamicAdSlot failed caused by unexpected field
     * @depends addDynamicAdSlot
     * @param ApiTester $I
     */
    public function editDynamicAdSlotWithUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/dynamicadslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'enableVariable' => true,
                'variableDescriptor_wrong' => [
                    'expressions' => [
                        [
                            'expression' => [
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
                            'expectAdSlot' => 23
                        ]
                    ]
                ]
            ]
        );
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

        $I->sendPATCH(URL_API . '/dynamicadslots/' . $item['id'], ['site' => PARAMS_SITE, 'height' => 250]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * patch dynamicAdSlot failed caused by variableDescriptor null
     * @depends addDynamicAdSlot
     * @param ApiTester $I
     */
    public function patchDynamicAdSlotWithVariableDescriptorNull(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/dynamicadslots/' . $item['id'],
            [
                'enableVariable' => true,
                'variableDescriptor' => null
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch dynamicAdSlot failed caused by variableDescriptor format wrong
     * @depends addDynamicAdSlot
     * @param ApiTester $I
     */
    public function patchDynamicAdSlotWithVariableDescriptorInvalid(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/dynamicadslots/' . $item['id'],
            [
                'enableVariable' => true,
                'variableDescriptor' => [
                    'expressions_wrong' => [
                        [
                            'expression' => [
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
                            'expectAdSlot' => 23
                        ]
                    ]
                ]
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch dynamicAdSlot failed caused by unexpected field
     * @depends addDynamicAdSlot
     * @param ApiTester $I
     */
    public function patchDynamicAdSlotWithUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/dynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/dynamicadslots/' . $item['id'],
            [
                'enableVariable' => true,
                'variableDescriptor_wrong' => [
                    'expressions' => [
                        [
                            'expression' => [
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
                            'expectAdSlot' => 23
                        ]
                    ]
                ]
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

    public function getDynamicAdTagsByAdSlot(ApiTester $I)
    {
        $I->sendGET(URL_API . '/dynamicadslots/' . PARAMS_AD_SLOT . '/adtags');
        $I->seeResponseCodeIs(200);
    }

//    public function addPositionsDynamicAdSlot(ApiTester $I) {
//        $I->sendPOST(URL_API.'/dynamicadslots/1/adtags/positions', []);
//        $I->seeResponseCodeIs(201);
//    }

    public function getJsAdTagsByDynamicAdSlot(ApiTester $I)
    {
        $I->sendGET(URL_API . '/dynamicadslots/' . PARAMS_AD_SLOT . '/jstag');
        $I->seeResponseCodeIs(200);
    }
}