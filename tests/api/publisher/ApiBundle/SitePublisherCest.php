<?php

/**
 * @group publisher
 */
class SitePublisherCest extends Site
{
    public function addSiteForPublisher(ApiTester $I) {
        $I->sendPOST(URL_API.'/sites', ['name' => 'Dtag.dev1', 'domain' => 'Dtag.dev1.dev']);
        $I->seeResponseCodeIs(201);
    }

    public function editSiteForPublisher(ApiTester $I) {
        $I->sendPUT(URL_API.'/sites/'.PARAMS_SITE, ['name' => 'Dtag.dev1', 'domain' => 'Dtag.dev1.dev']);
        $I->seeResponseCodeIs(204);
    }
}