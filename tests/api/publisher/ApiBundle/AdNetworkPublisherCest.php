<?php

/**
 * @group publisher
 */
class AdNetworkPublisherCest extends AdNetwork
{
    public function addAdNetwork(ApiTester $I) {
        $I->sendPOST(URL_API.'/adnetworks', [
            'defaultCpmRate' => 10,
            'name' => 'adNetwork-test',
            'url' => 'dtag-adnetwork-test.dev'
        ]);
        $I->seeResponseCodeIs(201);
    }

    /**
     * add AdNetwork Failed By Missing Field
     * @param ApiTester $I
     */
    public function addAdNetworkFailedByMissingField(ApiTester $I) {
        $I->sendPOST(URL_API.'/adnetworks', [
            'defaultCpmRate' => 10,
            //'name' => 'adNetwork-test', //missing this field
            'url' => 'dtag-adnetwork-test.dev',
            'active' => true
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add AdNetwork Failed By Unexpected Field
     * @param ApiTester $I
     */
    public function addAdNetworkFailedByUnexpectedField(ApiTester $I) {
        $I->sendPOST(URL_API.'/adnetworks', [
            'defaultCpmRate' => 10,
            'name' => 'adNetwork-test',
            'url' => 'dtag-adnetwork-test.dev',
            'active' => true,
            'unexpected_field' => true //unexpected field
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add AdNetwork Failed By Wrong Data
     * @param ApiTester $I
     */
    public function addAdNetworkFailedByWrongData(ApiTester $I) {
        $I->sendPOST(URL_API.'/adnetworks', [
            'defaultCpmRate' => -10, //wrong data, must be greater than 0
            'name' => 'adNetwork-test',
            'url' => 'dtag-adnetwork-test.dev',
            'active' => true
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add AdNetwork Failed By Wrong DataType
     * @param ApiTester $I
     */
    public function addAdNetworkFailedByWrongDataType(ApiTester $I) {
        $I->sendPOST(URL_API.'/adnetworks', [
            'defaultCpmRate' => '10_wrong', //wrong data type of this field
            'name' => 'adNetwork-test',
            'url' => 'dtag-adnetwork-test.dev',
            'active' => true
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit AdNetwork
     * @param ApiTester $I
     */
    public function editAdNetwork(ApiTester $I) {
        $I->sendPUT(URL_API.'/adnetworks/'.PARAMS_AD_NETWORK, [
            'defaultCpmRate' => 10,
            'name' => 'adNetwork-test',
            'url' => 'dtag-adnetwork-test.dev'
        ]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * edit AdNetwork failed by Unexpected Field
     * @param ApiTester $I
     */
    public function editAdNetworkFailedByUnexpectedField(ApiTester $I) {
        $I->sendPUT(URL_API.'/adnetworks/'.PARAMS_AD_NETWORK, [
            'defaultCpmRate' => 10,
            'name' => 'adNetwork-test',
            'url' => 'dtag-adnetwork-test.dev',
            'active' => true,
            'unexpected_field' => true //unexpected field
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit AdNetwork failed by Wrong Data
     * @param ApiTester $I
     */
    public function editAdNetworkFailedByWrongData(ApiTester $I) {
        $I->sendPUT(URL_API.'/adnetworks/'.PARAMS_AD_NETWORK, [
            'defaultCpmRate' => -10, //wrong data, must greater than 0
            'name' => 'adNetwork-test',
            'url' => 'dtag-adnetwork-test.dev',
            'active' => true
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit AdNetwork failed by Wrong DataType
     * @param ApiTester $I
     */
    public function editAdNetworkFailedByWrongDataType(ApiTester $I) {
        $I->sendPUT(URL_API.'/adnetworks/'.PARAMS_AD_NETWORK, [
            'defaultCpmRate' => '10_wrong', //wrong data type of this field
            'name' => 'adNetwork-test',
            'url' => 'dtag-adnetwork-test.dev',
            'active' => true
        ]);
        $I->seeResponseCodeIs(400);
    }
}