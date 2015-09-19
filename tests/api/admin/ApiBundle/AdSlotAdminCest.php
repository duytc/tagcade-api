<?php

/**
 * @group admin
 */
class AdSlotAdminCest extends AdSlot
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());

        self::$JSON_DATA_SAMPLE_AD_SLOT = [
            'site' => PARAMS_SITE,
            'libraryAdSlot' => [
                'width' => 200,
                'height' => 300,
                'name' => 'dtag.test.adslot',
                //'visible' => false //default
            ]
        ];
    }
}