<?php

class AdNetwork
{
    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I)
    {
    }

    /**
     * get All AdNetwork
     * @param ApiTester $I
     */
    public function getAllAdNetwork(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adnetworks');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get AdNetwork By Id
     * @param ApiTester $I
     */
    public function getAdNetworkById(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adnetworks/' . PARAMS_AD_NETWORK);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get AdNetwork By Id failed cause by Id NotExisted
     * @param ApiTester $I
     */
    public function getAdNetworkByIdNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adnetworks/' . '-1');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }

    /**
     * get All Site By AdNetwork
     * @param ApiTester $I
     */
    public function getAllSiteByAdNetwork(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adnetworks/' . PARAMS_AD_NETWORK . '/sites');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get All Site By AdNetwork failed cause by AdNetwork Not Existed
     * @param ApiTester $I
     */
    public function getAllSiteByAdNetworkNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adnetworks/' . '-1' . '/sites');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }

    /**
     * get WaterfallTag Active By AdNetwork
     * @param ApiTester $I
     */
    public function getAdTagActiveByAdNetwork(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adnetworks/' . PARAMS_AD_NETWORK . '/adtags/active');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get WaterfallTag Active By AdNetwork failed cause by AdNetwork Not Existed
     * @param ApiTester $I
     */
    public function getAdTagActiveByAdNetworkNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adnetworks/' . '-1' . '/adtags/active');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }

    /**
     * get Site Active By AdNetwork
     * @param ApiTester $I
     */
    public function getSiteActiveByAdNetwork(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adnetworks/' . PARAMS_AD_NETWORK . '/sites/active');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get Site Active By AdNetwork failed cause by AdNetwork Not Existed
     * @param ApiTester $I
     */
    public function getSiteActiveByAdNetworkNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adnetworks/' . '-1' . '/sites/active');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }

    /**
     * get WaterfallTag Active By Site And By AdNetwork
     * @param ApiTester $I
     */
    public function getAdTagActiveBySiteAndByAdNetwork(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adnetworks/' . PARAMS_AD_NETWORK . '/sites/' . PARAMS_SITE . '/adtags/active');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get WaterfallTag Active By Site And By AdNetwork failed cause by Site Not Existed
     * @param ApiTester $I
     */
    public function getAdTagActiveBySiteNotExistedAndByAdNetwork(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adnetworks/' . PARAMS_AD_NETWORK . '/sites/' . '-1' . '/adtags/active');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }

    /**
     * get WaterfallTag Active By Site And By AdNetwork failed cause by AdNetwork Not Existed
     * @param ApiTester $I
     */
    public function getAdTagActiveBySiteAndByAdNetworkNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adnetworks/' . '-1' . '/sites/' . PARAMS_SITE . '/adtags/active');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }

    /**
     * patch AdNetwork
     * @depends addAdNetwork
     */
    public function patchAdNetwork(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adnetworks');
        $items = $I->grabDataFromJsonResponse();

        $id = 0;
        foreach ($items as $item) {
            if ($item['id'] > $id) {
                $id = $item['id'];
            }
        };

        $I->sendPATCH(URL_API . '/adnetworks/' . $id, [
            'defaultCpmRate' => 12
        ]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * patch AdNetwork Failed By Wrong Data
     * @depends addAdNetwork
     */
    public function patchAdNetworkFailedByWrongData(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adnetworks');
        $items = $I->grabDataFromJsonResponse();

        $id = 0;
        foreach ($items as $item) {
            if ($item['id'] > $id) {
                $id = $item['id'];
            }
        };

        $I->sendPATCH(URL_API . '/adnetworks/' . $id, [
            'defaultCpmRate' => -12 //this is wrong data, must be greater than 0
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch AdNetwork Failed By Wrong DataType
     * @depends addAdNetwork
     */
    public function patchAdNetworkFailedByWrongDataType(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adnetworks');
        $items = $I->grabDataFromJsonResponse();

        $id = 0;
        foreach ($items as $item) {
            if ($item['id'] > $id) {
                $id = $item['id'];
            }
        };

        $I->sendPATCH(URL_API . '/adnetworks/' . $id, [
            'defaultCpmRate' => '12_wrong' //this is wrong data type, must be number and greater than 0
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch AdNetwork Failed By Unexpected Field
     * @depends addAdNetwork
     */
    public function patchAdNetworkFailedByUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adnetworks');
        $items = $I->grabDataFromJsonResponse();

        $id = 0;
        foreach ($items as $item) {
            if ($item['id'] > $id) {
                $id = $item['id'];
            }
        };

        $I->sendPATCH(URL_API . '/adnetworks/' . $id, [
            'defaultCpmRate_unexpected' => 12
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * delete AdNetwork
     * @depends addAdNetwork
     */
    public function deleteAdNetwork(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adnetworks');
        $items = $I->grabDataFromJsonResponse();

        $id = 0;
        foreach ($items as $item) {
            if ($item['id'] > $id) {
                $id = $item['id'];
            }
        };

        $I->sendDELETE(URL_API . '/adnetworks/' . $id);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete AdNetwork Not Existed
     * @depends addAdNetwork
     */
    public function deleteAdNetworkNotExisted(ApiTester $I)
    {
        $I->sendDELETE(URL_API . '/adnetworks/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * edit EstCpm of AdNetwork
     * @param ApiTester $I
     */
    public function editEstCpmAdNetwork(ApiTester $I)
    {
        $I->sendPUT(URL_API . '/adnetworks/' . PARAMS_AD_NETWORK . '/estcpm' . '?endDate='. END_DATE .'&estCpm='. '12' .'&startDate=' . START_DATE);
        $I->seeResponseCodeIs(204);
    }

    /**
     * edit EstCpm of AdNetwork failed by negative value
     * @param ApiTester $I
     */
    public function editEstCpmAdNetworkFailedByNegativeValue(ApiTester $I)
    {
        $I->sendPUT(URL_API . '/adnetworks/' . PARAMS_AD_NETWORK . '/estcpm' . '?endDate='. END_DATE .'&estCpm='. '-12' .'&startDate=' . START_DATE);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit EstCpm of AdNetwork failed by wrong data type
     * @param ApiTester $I
     */
    public function editEstCpmAdNetworkFailedByWrongDataType(ApiTester $I)
    {
        $I->sendPUT(URL_API . '/adnetworks/' . PARAMS_AD_NETWORK . '/estcpm' . '?endDate='. END_DATE .'&estCpm='. '12_wrong' .'&startDate=' . START_DATE);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit EstCpm By Site And ByAdNetwork
     * @param ApiTester $I
     */
    public function editEstCpmBySiteAndByAdNetwork(ApiTester $I)
    {
        $I->sendPUT(URL_API . '/adnetworks/' . PARAMS_AD_NETWORK . '/sites/' . PARAMS_SITE . '/estcpm' . '?endDate='. END_DATE .'&estCpm='. '15' .'&startDate=' . START_DATE);
        $I->seeResponseCodeIs(204);
    }

    /**
     * edit EstCpm By Site And ByAdNetwork by negative value
     * @param ApiTester $I
     */
    public function editEstCpmBySiteAndByAdNetworkFailedByNegativeValue(ApiTester $I)
    {
        $I->sendPUT(URL_API . '/adnetworks/' . PARAMS_AD_NETWORK . '/sites/' . PARAMS_SITE . '/estcpm' . '?endDate='. END_DATE .'&estCpm='. '-15' .'&startDate=' . START_DATE);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit EstCpm By Site And ByAdNetwork by Wrong Data Type
     * @param ApiTester $I
     */
    public function editEstCpmBySiteAndByAdNetworkFailedByWrongDataType(ApiTester $I)
    {
        $I->sendPUT(URL_API . '/adnetworks/' . PARAMS_AD_NETWORK . '/sites/' . PARAMS_SITE . '/estcpm' . '?endDate='. END_DATE .'&estCpm='. '15_wrong' .'&startDate=' . START_DATE);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit Status By Site And By AdNetwork
     * @param ApiTester $I
     */
    public function editStatusBySiteAndByAdNetwork(ApiTester $I)
    {
        $I->sendPUt(URL_API . '/adnetworks/' . PARAMS_AD_NETWORK . '/sites/' . PARAMS_SITE . '/status?active=0');
        $I->seeResponseCodeIs(204);
    }

    /**
     * edit Status By Site And By AdNetwork failed by status invalid
     * @param ApiTester $I
     */
    public function editStatusBySiteAndByAdNetworkFailedByStatusInvalid(ApiTester $I)
    {
        $I->sendPUt(URL_API . '/adnetworks/' . PARAMS_AD_NETWORK . '/sites/' . PARAMS_SITE . '/status?active=-1');
        $I->seeResponseCodeIs(400);
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