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
}