<?php

class AdTag
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I) {
    }

    public function getAllAdTag(ApiTester $I) {
        $I->sendGet(URL_API.'/adtags');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function getAdTagById(ApiTester $I) {
        $I->sendGet(URL_API.'/adtags/'.PARAMS_AD_TAG);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function addAdTag(ApiTester $I) {
        $I->sendPOST(URL_API.'/adtags', ['adSlot' => PARAMS_AD_SLOT, 'adNetwork' => PARAMS_AD_NETWORK, 'name' => 'adTag-test', 'html' => 'oki', 'frequencyCap' => 300, 'position' => 6, 'active' => true]);
        $I->seeResponseCodeIs(201);
    }

    public function deleteAdTag(ApiTester $I) {
        $I->sendGet(URL_API.'/adtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_API.'/adtags/'.$item['id']);
        $I->seeResponseCodeIs(204);
    }

    public function editAdTag(ApiTester $I) {
        $I->sendPUT(URL_API.'/adtags/'.PARAMS_AD_TAG, ['adSlot' => PARAMS_AD_SLOT, 'adNetwork' => PARAMS_AD_NETWORK, 'name' => 'adTag-test', 'html' => 'oki', 'frequencyCap' => 300, 'position' => 6, 'active' => true]);
        $I->seeResponseCodeIs(204);
    }

    public function editEstCpmAdTag(ApiTester $I) {
        $I->sendPUT(URL_API.'/adtags/'.PARAMS_AD_TAG.'/estcpm', ['estcpm' => 1]);
        $I->seeResponseCodeIs(204);
    }

    public function patchAdTag(ApiTester $I) {
        $I->sendPATCH(URL_API.'/adtags/'.PARAMS_AD_TAG, ['adSlot' => PARAMS_AD_SLOT, 'html' => 'oki', 'frequencyCap' => 300]);
        $I->seeResponseCodeIs(204);
    }
}