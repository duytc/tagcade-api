<?php

/**
 * @group publisher
 */
class SitePublisherCest extends Site
{
    /**
     * add site
     * @param ApiTester $I
     */
    public function addSite(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/sites',
            [
                'name' => 'Dtag.dev1',
                'domain' => 'dtag1.dev',
                'channelSites' => [],
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
                'name' => 'Dtag.dev1',
                'domain' => 'dtag1.dev',
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
                'name' => null, //this field is null
                'domain' => 'dtag1.dev',
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
                'name' => 'Dtag.dev1',
                'domain' => 'dtag1.dev',
                'channelSites' => [],
                'unexpected_field' => 'Dtag.dev1' //this is unexpected field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    public function editSite(ApiTester $I)
    {
        $I->sendPUT(URL_API . '/sites/' . PARAMS_SITE, [
                'name' => 'Dtag.dev1',
                'domain' => 'dtag2.dev'
            ]
        );
        $I->seeResponseCodeIs(204);
    }
}