<?php

class AdTag
{
    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I)
    {
    }

    /**
     * get All AdTagData
     * @param ApiTester $I
     */
    public function getAllAdTagData(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtagdatas');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get AdTagData By Id
     * @param ApiTester $I
     */
    public function getAdTagDataById(ApiTester $I) {
        $I->sendGet(URL_API.'/adtagdatas/'.PARAMS_AD_TAG);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get AdTagData By Id Not Existed
     * @param ApiTester $I
     */
    public function getAdTagDataByIdNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtagdatas/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * add AdTagData
     * @param ApiTester $I
     */
    public function addAdTagData(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adtagdatas', [
            'adNetwork' => PARAMS_AD_NETWORK,
            'html' => 'oki',
            'inLibrary' => false
        ]);
        $I->seeResponseCodeIs(201);
    }

    /**
     * add AdTagData failed by missing field
     * @param ApiTester $I
     */
    public function addAdTagDataMissingField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adtagdatas', [
            'adNetwork' => PARAMS_AD_NETWORK,
            //'html' => 'oki',
            'inLibrary' => false
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add AdTagData failed by null field
     * @param ApiTester $I
     */
    public function addAdTagDataNullField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adtags', [
            'adNetwork' => PARAMS_AD_NETWORK,
            'html' => null,
            'inLibrary' => false
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add AdTagData failed by wrong data type
     * @param ApiTester $I
     */
    public function addAdTagDataWrongDataType(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adtagdatas', [
            'adNetwork' => PARAMS_AD_NETWORK,
            'html' => 'oki',
            'inLibrary' => 'false' // wrong data type
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add AdTagData failed by wrong data
     * @param ApiTester $I
     */
    public function addAdTagDataWrongData(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adtagdatas', [
            'adNetwork' => PARAMS_AD_NETWORK,
            'html' => true, // wrong data
            'inLibrary' => false
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add AdTagData failed by contains unexpected field
     * @param ApiTester $I
     */
    public function addAdTagDataWithUnexpectedField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adtagdatas', [
            'adNetwork' => PARAMS_AD_NETWORK,
            'unexpected_field' => 'adTag-test', //this is unexpected field
            'html' => 'oki',
            'inLibrary' => false
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit AdTagData
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function editAdTagData(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtagdatas');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/adtagdatas/' . $item['id'], [
            'adNetwork' => PARAMS_AD_NETWORK,
            'html' => 'okies',
            'inLibrary' => true]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * edit AdTagData failed by null field
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function editAdTagDataWithNullField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtagdatas');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/adtagdatas/' . $item['id'], [
            'adNetwork' => PARAMS_AD_NETWORK,
            'html' => null, //this is null field
            'inLibrary' => true]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit AdTagData failed by unexpected field
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function editAdTagDataWithUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtagdatas');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/adtagdatas/' . $item['id'], [
            'adNetwork' => PARAMS_AD_NETWORK,
            'unexpected_field' => 'adTag-test', //this is unexpected field
            'html' => 'oki',
            'inLibrary' => true]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit AdTagData failed by wrong data
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function editAdTaDatagWithWrongData(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtagdatas');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/adtagdatas/' . $item['id'], [
            'adNetwork' => PARAMS_AD_NETWORK,
            'html' => 'oki',
            'inLibrary' => 'true']);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit AdTagData failed by wrong data type
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function editAdTagDataWithWrongDataType(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtagdatas');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/adtagdatas/' . $item['id'], [
            'adNetwork' => PARAMS_AD_NETWORK,
            'html' => true,
            'active' => true]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch AdTagData
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function patchAdTagData(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtagdatas');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/adtagdatas/' . $item['id'], [
            'adNetwork' => PARAMS_AD_NETWORK,
            'html' => 'ok done',
        ]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * patch AdTagData failed by null field
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function patchAdTagDataNullField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtagdatas');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/adtagdatas/' . $item['id'], [
            'html' => null, //this is field null
            'inLibrary' => true
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch AdTagData failed by unexpected field
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function patchAdTagDataWithUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtagdatas');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/adtagdatas/' . $item['id'], [
            'html' => 'oki',
            'unexpected_field' => 'oki', //this is unexpected field
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch AdTagData failed by wrong data
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function patchAdTagDataWithWrongData(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtagdatas');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/adtagdatas/' . $item['id'], [
            'html' => 'oki',
            'inLibrary' => 'false' //this is wrong data, must boolean
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch AdTagData failed by wrong data type
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function patchAdTagDataWithWrongDataType(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtagdatas');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/adtagdatas/' . $item['id'], [
            'html' => true,
            'inLibrary' => false //this is wrong data type, must number
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * delete AdTagData
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function deleteAdTagData(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtagdatas');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_API . '/adtagdatas/' . $item['id']);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete AdTagData failed cause by Not Existed
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function deleteAdTagDataNotExisted(ApiTester $I)
    {
        $I->sendDELETE(URL_API . '/adtagdatas/' . '-1');
        $I->seeResponseCodeIs(404);
    }
}