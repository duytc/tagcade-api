<?php

class AdSlot
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I) {
    }

    public function getAllAdSlot(ApiTester $I) {
        $I->sendGet(URL_API.'/adslots');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function getAdSlotById(ApiTester $I) {
        $I->sendGet(URL_API.'/adslots/'.PARAMS_AD_SLOT);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function addAdSlot(ApiTester $I) {
        $I->sendPOST(URL_API.'/adslots', ['site' => PARAMS_SITE, 'name' => 'dtag.test.adslot', 'height' => 200, 'width' => 300]);
        $I->seeResponseCodeIs(201);
    }

    /**
     * @depends addAdSlot
     */
    public function editAdSlot(ApiTester $I) {
        $I->sendGet(URL_API.'/adslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API.'/adslots/'.$item['id'], ['site' => PARAMS_SITE, 'name' => 'dtag.test.adslot', 'height' => 200, 'width' => 300]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * @depends addAdSlot
     */
    public function patchAdSlot(ApiTester $I) {
        $I->sendGet(URL_API.'/adslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API.'/adslots/'.$item['id'], ['site' => PARAMS_SITE, 'height' => 250]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * @depends addAdSlot
     */
    public function deleteAdSlot(ApiTester $I) {
        $I->sendGet(URL_API.'/adslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_API.'/adslots/'.$item['id']);
        $I->seeResponseCodeIs(204);
    }

    public function getAdTagsByAdSlot(ApiTester $I) {
        $I->sendGET(URL_API.'/adslots/'.PARAMS_AD_SLOT.'/adtags');
        $I->seeResponseCodeIs(200);
    }

//    public function addPositionsAdSlot(ApiTester $I) {
//        $I->sendPOST(URL_API.'/adslots/1/adtags/positions', []);
//        $I->seeResponseCodeIs(201);
//    }

    public function getJsAdTagsByAdSlot(ApiTester $I) {
        $I->sendGET(URL_API.'/adslots/'.PARAMS_AD_SLOT.'/jstag');
        $I->seeResponseCodeIs(200);
    }
}