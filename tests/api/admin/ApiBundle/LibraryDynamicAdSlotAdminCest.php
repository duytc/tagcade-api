<?php

/**
 * @group admin
 */
class LibraryDynamicAdSlotAdminCest extends LibraryDynamicAdSlot
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());

        self::$JSON_DATA_SAMPLE_LIBRARY_DYNAMIC_AD_SLOT = [
            'publisher' => PARAMS_PUBLISHER,
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

    public function patchLibraryDynamicAdSlotWithNewLibraryNativeAdSlot(ApiTester $I)
    {
        $jsonData =  [
            'name' => 'dtag.test.librarynativeadslot',
            'publisher' => PARAMS_PUBLISHER
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
        $jsonDynamic['publisher'] = PARAMS_PUBLISHER;
        $I->sendPOST(URL_API . '/librarydynamicadslots', $jsonDynamic);
        $I->seeResponseCodeIs(201);

        $I->sendGet(URL_API . '/librarydynamicadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/librarydynamicadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(204);
    }
}