<?php
/**
 * @group admin
 */
class AdNetworkAdminCest extends AdNetwork
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }

    public function addAdNetworkForAdmin(ApiTester $I) {
        $I->sendPOST(URL_API.'/adnetworks', ['defaultCpmRate' => 10, 'name' => 'adNetwork-test', 'publisher' => PARAMS_PUBLISHER, 'url' => 'dtag-adnetwork-test.dev', 'active' => true]);
        $I->seeResponseCodeIs(201);
    }

    public function editAdNetworkForAdmin(ApiTester $I) {
        $I->sendPUT(URL_API.'/adnetworks/'.PARAMS_AD_NETWORK, ['publisher' => PARAMS_PUBLISHER, 'defaultCpmRate' => 10, 'name' => 'adNetwork-test', 'url' => 'dtag-adnetwork-test.dev', 'active' => true]);
        $I->seeResponseCodeIs(204);
    }

//    public function addPositionsAdNetwork(ApiTester $I) {
//        $I->sendPOST(URL_API.'/adnetworks/'.PARAMS_AD_NETWORK.'/positions', []);
//        $I->seeResponseCodeIs(201);
//    }

//    public function addPositionsForSiteAndAdNetwork(ApiTester $I) {
//        $I->sendPOST(URL_API.'/adnetworks/'.PARAMS_AD_NETWORK.'/sites/'.PARAMS_SITE.'/positions', []);
//        $I->seeResponseCodeIs(201);
//    }

}