<?php

/**
 * @group admin
 */
class SiteAdminCest extends Site
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }

    public function addSite(ApiTester $I) {
        $I->sendPOST(URL_API.'/sites', ['publisher' => PARAMS_PUBLISHER, 'name' => 'Dtag.dev1', 'domain' => 'Dtag.dev1.dev']);
        $I->seeResponseCodeIs(201);
    }

    /**
     * @depends addSite
     */
    public function editSite(ApiTester $I) {
        $I->sendGet(URL_API.'/sites');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API.'/sites/'.$item['id'], ['publisher' => PARAMS_PUBLISHER, 'name' => 'Dtag.dev1', 'domain' => 'Dtag.dev1.dev']);
        $I->seeResponseCodeIs(204);
    }
}