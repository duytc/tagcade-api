<?php

class AdNetwork
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I) {
    }

    public function getAllAdNetwork(ApiTester $I) {
        $I->sendGet(URL_API.'/adnetworks');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function getAdNetworkById(ApiTester $I) {
        $I->sendGet(URL_API.'/adnetworks/'.PARAMS_AD_NETWORK);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function getAllSiteByAdNetwork(ApiTester $I) {
        $I->sendGet(URL_API.'/adnetworks/'.PARAMS_AD_NETWORK.'/sites');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function getAdTagActiveByAdNetwork(ApiTester $I) {
        $I->sendGet(URL_API.'/adnetworks/'.PARAMS_AD_NETWORK.'/adtags/active');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function getSiteActiveByAdNetwork(ApiTester $I) {
        $I->sendGet(URL_API.'/adnetworks/'.PARAMS_AD_NETWORK.'/sites/active');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function getAdTagActiveBySiteAndByAdNetwork(ApiTester $I) {
        $I->sendGet(URL_API.'/adnetworks/'.PARAMS_AD_NETWORK.'/sites/'.PARAMS_SITE.'/adtags/active');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }


    public function deleteAdNetwork(ApiTester $I) {
        $I->sendGet(URL_API.'/adnetworks');
        $items = $I->grabDataFromJsonResponse();

        $id = 0;
        foreach($items as $item) {
            if($item['id'] > $id) {
                $id = $item['id'];
            }
        };

        $I->sendDELETE(URL_API.'/adnetworks/'.$id);
        $I->seeResponseCodeIs(204);
    }


    public function editEstCpmAdNetwork(ApiTester $I) {
        $I->sendPUT(URL_API.'/adnetworks/'.PARAMS_AD_NETWORK.'/estcpm', ['estcpm' => 12]);
        $I->seeResponseCodeIs(204);
    }

    public function editEstCpmBySiteAndByAdNetwork(ApiTester $I) {
        $I->sendPUt(URL_API.'/adnetworks/'.PARAMS_AD_NETWORK.'/sites/'.PARAMS_SITE.'/estcpm', ['estcpm' => 15]);
        $I->seeResponseCodeIs(204);
    }

    public function editStatusBySiteAndByAdNetwork(ApiTester $I) {
        $I->sendPUt(URL_API.'/adnetworks/'.PARAMS_AD_NETWORK.'/sites/'.PARAMS_SITE.'/status?active=0');
        $I->seeResponseCodeIs(204);
    }

    public function patchAdNetwork(ApiTester $I) {
        $I->sendPATCH(URL_API.'/adnetworks/'.PARAMS_AD_NETWORK, ['defaultCpmRate' => 12]);
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