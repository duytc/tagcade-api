<?php

/**
 * @group admin
 */
class SiteAdminCest extends Site
{
    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getAdminToken());
    }

    /**
     * add site
     * @param ApiTester $I
     */
    public function addSite(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/sites',
            [
                'publisher' => PARAMS_PUBLISHER,
                'name' => 'Dtag.dev1',
                'domain' => 'Dtag.dev1.dev',
                'channelSites' => []
            ]
        );
        $I->seeResponseCodeIs(201);
    }

    /**
     * add Site With Channel
     * @param ApiTester $I
     */
    public function addSiteWithChannel(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/sites',
            [
                'publisher' => PARAMS_PUBLISHER,
                'name' => 'Dtag.dev1',
                'domain' => 'Dtag.dev1.dev',
                'channelSites' => [
                    ['channel' => PARAMS_CHANNEL]
                ]
            ]
        );
        $I->seeResponseCodeIs(201);
    }

    /**
     * add site failed by field null
     * @param ApiTester $I
     */
    public function addSiteWithFieldNull(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/sites',
            [
                'publisher' => PARAMS_PUBLISHER,
                'name' => null, //this field is null
                'domain' => 'Dtag.dev1.dev',
                'channelSites' => []
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add site failed by unexpected field
     * @param ApiTester $I
     */
    public function addSiteWithUnexpectedField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/sites',
            [
                'publisher' => PARAMS_PUBLISHER,
                'name' => 'Dtag.dev1',
                'domain' => 'Dtag.dev1.dev',
                'channelSites' => [],
                'unexpected_field' => 'Dtag.dev1' //this is unexpected field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit Site
     * @depends addSite
     * @param ApiTester $I
     */
    public function editSite(ApiTester $I)
    {
        $I->sendGet(URL_API . '/sites');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/sites/' . $item['id'],
            [
                'publisher' => PARAMS_PUBLISHER,
                'name' => 'Dtag.dev1',
                'domain' => 'Dtag.dev1.dev'
            ]
        );
        $I->seeResponseCodeIs(204);
    }

    /**
     * edit Site failed by field null
     * @depends addSite
     * @param ApiTester $I
     */
    public function editSiteWithFieldNull(ApiTester $I)
    {
        $I->sendGet(URL_API . '/sites');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/sites/' . $item['id'],
            [
                'publisher' => PARAMS_PUBLISHER,
                'name' => null, //this field is null
                'domain' => 'Dtag.dev1.dev'
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit Site failed by Unexpected Field
     * @depends addSite
     * @param ApiTester $I
     */
    public function editSiteWithUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/sites');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/sites/' . $item['id'],
            [
                'publisher' => PARAMS_PUBLISHER,
                'name' => 'Dtag.dev1',
                'domain' => 'Dtag.dev1.dev',
                'unexpected_field' => 'Dtag.dev1' //this is unexpected field
            ]
        );
        $I->seeResponseCodeIs(400);
    }
}