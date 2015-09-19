<?php

/**
 * @group admin
 */
class LibraryNativeAdSlotAdminCest extends LibraryNativeAdSlot
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());

        self::$JSON_DATA_SAMPLE_LIBRARY_NATIVE_AD_SLOT = [
            'name' => 'dtag.test.librarydisplayadslot',
            //'visible' => true, //default
            'publisher' => PARAMS_PUBLISHER
        ];
    }
}