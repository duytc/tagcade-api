<?php

/**
 * @group admin
 */
class AdSlotAdminCest extends AdSlot
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }

//    public function addPositionsAdSlot(ApiTester $I) {
//        $I->sendPOST(URL_API.'/adslots/1/adtags/positions', []);
//        $I->seeResponseCodeIs(201);
//    }
}