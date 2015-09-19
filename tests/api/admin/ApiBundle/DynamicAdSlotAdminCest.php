<?php

/**
 * @group admin
 */
class DynamicAdSlotAdminCest extends DynamicAdSlot
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());

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
}