<?php

/**
 * @group admin
 */
class SiteAdminCest extends Site
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }

    public function addSiteForAdmin(ApiTester $I) {
        $I->sendPOST(URL_API.'/sites', ['publisher' => PARAMS_PUBLISHER, 'name' => 'Dtag.dev1', 'domain' => 'Dtag.dev1.dev']);
        $I->seeResponseCodeIs(201);
    }

    public function editSiteForAdmin(ApiTester $I) {
        $I->sendPUT(URL_API.'/sites/'.PARAMS_SITE, ['publisher' => PARAMS_PUBLISHER, 'name' => 'Dtag.dev1', 'domain' => 'Dtag.dev1.dev']);
        $I->seeResponseCodeIs(204);
    }
}