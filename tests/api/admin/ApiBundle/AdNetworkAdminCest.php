<?php
/**
 * @group admin
 */
class AdNetworkAdminCest extends AdNetwork
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }

    public function addAdNetwork(ApiTester $I) {
        $I->sendPOST(URL_API.'/adnetworks', ['defaultCpmRate' => 10, 'name' => 'adNetwork-test', 'publisher' => PARAMS_PUBLISHER, 'url' => 'dtag-adnetwork-test.dev', 'active' => true]);
        $I->seeResponseCodeIs(201);
    }

    /**
     * @depends addAdNetwork
     */
    public function editAdNetwork(ApiTester $I) {
        $I->sendGet(URL_API.'/adnetworks');
        $items = $I->grabDataFromJsonResponse();
        $id = 0;
        foreach($items as $item) {
            if($item['id'] > $id) {
                $id = $item['id'];
            }
        };

        $I->sendPUT(URL_API.'/adnetworks/'.$id, ['publisher' => PARAMS_PUBLISHER, 'defaultCpmRate' => 10, 'name' => 'adNetwork-test', 'url' => 'dtag-adnetwork-test.dev', 'active' => true]);
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