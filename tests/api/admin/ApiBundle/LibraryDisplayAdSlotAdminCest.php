<?php

/**
 * @group admin
 */
class LibraryDisplayAdSlotAdminCest extends LibraryDisplayAdSlot
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());

        self::$JSON_DATA_SAMPLE_LIBRARY_AD_SLOT = [
            'width' => 200,
            'height' => 300,
            'name' => 'dtag.test.librarydisplayadslot',
            //'visible' => true, //default
            'publisher' => PARAMS_PUBLISHER
        ];
    }
}