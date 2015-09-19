<?php

/**
 * @group admin
 */
class NativeAdSlotAdminCest extends NativeAdSlot
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());

        self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT = [
            'site' => PARAMS_SITE,
            'libraryAdSlot' => [
                'name' => 'dtag.test.nativeAdslot',
                //'visible' => false //default
            ]
        ];
    }
}