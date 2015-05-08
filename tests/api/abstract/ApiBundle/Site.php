<?php

class Site
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I) {
    }

    public function getAllSite(ApiTester $I) {
        $I->sendGet(URL_API.'/sites');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function getSiteById(ApiTester $I) {
        $I->sendGet(URL_API.'/sites/'.PARAMS_SITE);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * @depends addSite
     */
    public function patchSite(ApiTester $I) {
        $I->sendGet(URL_API.'/sites');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API.'/sites/'.$item['id'], ['name' => 'Dtag.dev1']);
        $I->seeResponseCodeIs(204);
    }

    /**
     * @depends addSite
     */
    public function deleteSite(ApiTester $I) {
        $I->sendGet(URL_API.'/sites');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_API.'/sites/'.$item['id']);
        $I->seeResponseCodeIs(204);
    }

    public function getAdSlotsBySite(ApiTester $I) {
        $I->sendGET(URL_API.'/sites/'.PARAMS_SITE.'/adslots');
        $I->seeResponseCodeIs(200);
    }

    public function getAdTagsActiveBySite(ApiTester $I) {
        $I->sendGET(URL_API.'/sites/'.PARAMS_SITE.'/adtags/active');
        $I->seeResponseCodeIs(200);
    }

    public function getJsTagsBySite(ApiTester $I) {
        $I->sendGET(URL_API.'/sites/'.PARAMS_SITE.'/jstags');
        $I->seeResponseCodeIs(200);
    }
}