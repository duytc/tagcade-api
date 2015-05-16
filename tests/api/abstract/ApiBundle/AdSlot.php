<?php

class AdSlot
{
    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I)
    {
    }

    /**
     * get All AdSlot
     * @param ApiTester $I
     */
    public function getAllAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adslots');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get AdSlot By Id
     * @param ApiTester $I
     */
    public function getAdSlotById(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adslots/' . PARAMS_AD_SLOT);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get AdSlot By Id Not Existed
     * @param ApiTester $I
     */
    public function getAdSlotByIdNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adslots/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * add AdSlot
     * @param ApiTester $I
     */
    public function addAdSlot(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adslots',
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'height' => 200, 'width' => 300
            ]
        );
        $I->seeResponseCodeIs(201);
    }

    /**
     * add adSlot failed caused by name null
     * @param ApiTester $I
     */
    public function addAdSlotWithNameNull(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adslots',
            [
                'site' => PARAMS_SITE,
                'name' => null,
                'height' => 200, 'width' => 300
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by width or height format wrong
     * @param ApiTester $I
     */
    public function addAdSlotWithWidthOrHeightInvalid(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adslots',
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'height' => '200_invalid', 'width' => '300_invalid'
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by missing field
     * @param ApiTester $I
     */
    public function addAdSlotMissingField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adslots',
            [
                'site' => PARAMS_SITE,
                //'name' => null, //this is missing field
                'height' => 200, 'width' => 300
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by contains unexpected field
     * @param ApiTester $I
     */
    public function addAdSlotWithWithUnexpectedField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adslots',
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'height' => 200, 'width' => 300,
                'unexpected_field' => 'unexpected_field' //this is unexpected field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * clone adSlot
     * @param ApiTester $I
     */
    public function cloneAdSlot(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adslots/' . PARAMS_AD_SLOT . '/clone',
            [
                'name' => 'dtag.test.adslot-clone',
            ]
        );
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    /**
     * clone adSlot failed cause by id not existed
     * @param ApiTester $I
     */
    public function cloneAdSlotNotExisted(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adslots/' . '-1' . '/clone',
            [
                'name' => 'dtag.test.adslot-clone',
            ]
        );
        $I->seeResponseCodeIs(404);
    }

    /**
     * clone adSlot failed caused by null field
     * @param ApiTester $I
     */
    public function cloneAdSlotWithNullField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adslots/' . PARAMS_AD_SLOT . '/clone',
            [
                'name' => null //this is null field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * clone adSlot failed cause by wrong data
     * @param ApiTester $I
     */
    public function cloneAdSlotWithWrongData(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adslots/' . PARAMS_AD_SLOT . '/clone',
            [
                'name' => "" //this is wrong data type field, must not empty
            ]
        );
        $I->seeResponseCodeIs(400);
    }

//    /**
//     * clone adSlot failed cause by wrong data type
//     * @param ApiTester $I
//     */
//    public function cloneAdSlotWithWrongDataType(ApiTester $I)
//    {
//        $I->sendPOST(URL_API . '/adslots/' . PARAMS_AD_SLOT . '/clone',
//            [
//                'name' => (int) 123 //this is wrong data type field, must string
//            ]
//        );
//        $I->seeResponseCodeIs(400);
//    }

    /**
     * clone adSlot failed cause by missing field
     * @param ApiTester $I
     */
    public function cloneAdSlotWithWithMissingField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adslots/' . PARAMS_AD_SLOT . '/clone',
            [
                'unexpected_field' => 'unexpected_field' //this is unexpected field, require 'name' field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * @depends addAdSlot
     */
    public function editAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/adslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'height' => 200, 'width' => 300
            ]
        );
        $I->seeResponseCodeIs(204);
    }

    /**
     * add adSlot failed caused by name null
     * @param ApiTester $I
     */
    public function editAdSlotWithNameNull(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/adslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'name' => null,
                'height' => 200, 'width' => 300
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by width or height format wrong
     * @param ApiTester $I
     */
    public function editAdSlotWithWidthOrHeightInvalid(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/adslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'height' => '200_wrong', 'width' => '300_wrong'
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by unexpected field
     * @param ApiTester $I
     */
    public function editAdSlotWithUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/adslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot',
                'height' => 200, 'width' => 300,
                'unexpected_field' => 'unexpected_field' //this is unexpected field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * @depends addAdSlot
     */
    public function patchAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/adslots/' . $item['id'], ['site' => PARAMS_SITE, 'height' => 250]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * add adSlot failed caused by name null
     * @param ApiTester $I
     */
    public function patchAdSlotWithNameNull(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/adslots/' . $item['id'],
            [
                'name' => null
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by width or height format wrong
     * @param ApiTester $I
     */
    public function patchAdSlotWithWidthOrHeightInvalid(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/adslots/' . $item['id'],
            [
                'height' => '250_wrong',
                'width' => '250_wrong'
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by unexpected field
     * @param ApiTester $I
     */
    public function patchAdSlotWithUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/adslots/' . $item['id'],
            [
                'unexpected_field' => 'unexpected_field' //this is unexpected field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * @depends addAdSlot
     */
    public function deleteAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_API . '/adslots/' . $item['id']);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete AdSlot Not Existed
     * @depends addAdSlot
     */
    public function deleteAdSlotNotExisted(ApiTester $I)
    {
        $I->sendDELETE(URL_API . '/adslots/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * get AdTags By AdSlot
     * @param ApiTester $I
     */
    public function getAdTagsByAdSlot(ApiTester $I)
    {
        $I->sendGET(URL_API . '/adslots/' . PARAMS_AD_SLOT . '/adtags');
        $I->seeResponseCodeIs(200);
    }

    /**
     * get AdTags By AdSlot failed cause by Not Existed
     * @param ApiTester $I
     */
    public function getAdTagsByAdSlotNotExisted(ApiTester $I)
    {
        $I->sendGET(URL_API . '/adslots/' . '-1' . '/adtags');
        $I->seeResponseCodeIs(404);
    }

//    public function addPositionsAdSlot(ApiTester $I) {
//        $I->sendPOST(URL_API.'/adslots/1/adtags/positions', []);
//        $I->seeResponseCodeIs(201);
//    }

    /**
     * get Js AdTags By AdSlot
     * @param ApiTester $I
     */
    public function getJsAdTagsByAdSlot(ApiTester $I)
    {
        $I->sendGET(URL_API . '/adslots/' . PARAMS_AD_SLOT . '/jstag');
        $I->seeResponseCodeIs(200);
    }

    /**
     * get Js AdTags By AdSlot failed cause by Not Existed
     * @param ApiTester $I
     */
    public function getJsAdTagsByAdSlotNotExisted(ApiTester $I)
    {
        $I->sendGET(URL_API . '/adslots/' . '-1' . '/jstag');
        $I->seeResponseCodeIs(404);
    }
}