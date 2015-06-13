<?php

/**
 * @group admin
 */
class NativeAdSlotAdminCest extends NativeAdSlot
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }

//    public function addPositionsAdSlot(ApiTester $I) {
//        $I->sendPOST(URL_API.'/adslots/1/adtags/positions', []);
//        $I->seeResponseCodeIs(201);
//    }
}