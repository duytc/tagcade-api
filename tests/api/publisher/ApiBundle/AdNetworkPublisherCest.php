<?php

/**
 * @group publisher
 */
class AdNetworkPublisherCest extends AdNetwork
{
    public function addAdNetworkForPublisher(ApiTester $I) {
        $I->sendPOST(URL_API.'/adnetworks', ['defaultCpmRate' => 10, 'name' => 'adNetwork-test', 'url' => 'dtag-adnetwork-test.dev', 'active' => true]);
        $I->seeResponseCodeIs(201);
    }

    public function editAdNetworkForPublisher(ApiTester $I) {
        $I->sendPUT(URL_API.'/adnetworks/'.PARAMS_AD_NETWORK, ['defaultCpmRate' => 10, 'name' => 'adNetwork-test', 'url' => 'dtag-adnetwork-test.dev', 'active' => true]);
        $I->seeResponseCodeIs(204);
    }
}