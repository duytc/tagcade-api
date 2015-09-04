<?php
/**
 * @group admin
 */
class AdNetworkAdminCest extends AdNetwork
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }

    /**
     * add AdNetwork
     * @param ApiTester $I
     */
    public function addAdNetwork(ApiTester $I) {
        $I->sendPOST(URL_API.'/adnetworks', [
            'defaultCpmRate' => 10,
            'name' => 'adNetwork-test',
            'publisher' => PARAMS_PUBLISHER,
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
            'publisher' => PARAMS_PUBLISHER,
            'url' => 'dtag-adnetwork-test.dev'
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
            'publisher' => PARAMS_PUBLISHER,
            'url' => 'dtag-adnetwork-test.dev',
            'unexpected_field' => true //unexpected field
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
            'publisher' => PARAMS_PUBLISHER,
            'url' => 'dtag-adnetwork-test.dev'
        ]);
        $I->seeResponseCodeIs(400);
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

        $I->sendPUT(URL_API.'/adnetworks/'.$id, [
            'defaultCpmRate' => 10,
            'name' => 'adNetwork-test',
            'publisher' => PARAMS_PUBLISHER,
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
            'publisher' => PARAMS_PUBLISHER,
            'url' => 'dtag-adnetwork-test.dev',
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
            'publisher' => PARAMS_PUBLISHER,
            'url' => 'dtag-adnetwork-test.dev'
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
            'publisher' => PARAMS_PUBLISHER,
            'url' => 'dtag-adnetwork-test.dev'
        ]);
        $I->seeResponseCodeIs(400);
    }
}