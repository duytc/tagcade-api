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
        $I->sendGet(URL_API . '/displayadslots');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get AdSlot By Id
     * @param ApiTester $I
     */
    public function getAdSlotById(ApiTester $I)
    {
        $I->sendGet(URL_API . '/displayadslots/' . PARAMS_AD_SLOT);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get AdSlot By Id Not Existed
     * @param ApiTester $I
     */
    public function getAdSlotByIdNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/displayadslots/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * add AdSlot
     * @param ApiTester $I
     */
    public function addAdSlot(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/displayadslots',
            [
                'site' => PARAMS_SITE,
                'libraryAdSlot' => [
                    'width' => 200,
                    'height' => 300,
                    'name' => 'dtag.test.adslot',
                    'visible' => false
                ]
            ]
        );
        $I->seeResponseCodeIs(201);
    }

    /**
     * add adSlot failed caused by name null
     * @param ApiTester $I
     */
    public function addAdSlotWithLibraryAdSlotNull(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/displayadslots',
            [
                'site' => PARAMS_SITE,
                'libraryAdSlot' => null //this is null field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by name null
     * @param ApiTester $I
     */
    public function addAdSlotWithNameNull(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/displayadslots',
            [
                'site' => PARAMS_SITE,
                'libraryAdSlot' => [
                    'width' => 200,
                    'height' => 300,
                    'name' => null, //this is null field
                    'visible' => false
                ]
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
        $I->sendPOST(URL_API . '/displayadslots',
            [
                'site' => PARAMS_SITE,
                'libraryAdSlot' => [
                    'width' => '300_invalid',
                    'height' => '200_invalid',
                    'name' => 'dtag.test.adslot',
                    'visible' => false
                ]
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
        $I->sendPOST(URL_API . '/displayadslots',
            [
                'site' => PARAMS_SITE,
                //'libraryAdSlot' => [
                //    'width' => 200,
                //    'height' => 300,
                //    'name' => 'dtag.test.adslot',
                //    'visible' => false
                //] //this is missing field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by contains unexpected field
     * @param ApiTester $I
     */
    public function addAdSlotWithUnexpectedField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/displayadslots',
            [
                'site' => PARAMS_SITE,
                'libraryAdSlot' => [
                    'width' => 200,
                    'height' => 300,
                    'name' => 'dtag.test.adslot',
                    'visible' => false
                ],
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
        $I->sendPOST(URL_API . '/displayadslots/' . PARAMS_AD_SLOT . '/clone',
            [
                'site' => PARAMS_SITE,
                'name' => 'dtag.test.adslot-clone'
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
        $I->sendPOST(URL_API . '/displayadslots/' . '-1' . '/clone',
            [
                'site' => PARAMS_SITE,
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
        $I->sendPOST(URL_API . '/displayadslots/' . PARAMS_AD_SLOT . '/clone',
            [
                'site' => PARAMS_SITE,
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
        $I->sendPOST(URL_API . '/displayadslots/' . PARAMS_AD_SLOT . '/clone',
            [
                'site' => PARAMS_SITE,
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
//        $I->sendPOST(URL_API . '/displayadslots/' . PARAMS_AD_SLOT . '/clone',
//            [
//                'site' => PARAMS_SITE,
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
        $I->sendPOST(URL_API . '/displayadslots/' . PARAMS_AD_SLOT . '/clone',
            [
                'site' => PARAMS_SITE,
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
        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/displayadslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'libraryAdSlot' => [
                    'width' => 200,
                    'height' => 300,
                    'name' => 'dtag.test.adslot',
                    'visible' => false
                ]
            ]
        );
        $I->seeResponseCodeIs(204);
    }

    /**
     * add adSlot failed caused by name null
     * @param ApiTester $I
     */
    public function editAdSlotWithLibraryAdSlotNull(ApiTester $I)
    {
        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/displayadslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'libraryAdSlot' => null
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by name null
     * @param ApiTester $I
     */
    public function editAdSlotWithNameNull(ApiTester $I)
    {
        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/displayadslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'libraryAdSlot' => [
                    'width' => 200,
                    'height' => 300,
                    'name' => null,
                    'visible' => false
                ]
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
        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/displayadslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'libraryAdSlot' => [
                    'width' => '300_wrong',
                    'height' => '200_wrong',
                    'name' => 'dtag.test.adslot',
                    'visible' => false
                ]
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
        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/displayadslots/' . $item['id'],
            [
                'site' => PARAMS_SITE,
                'libraryAdSlot' => [
                    'width' => 200,
                    'height' => 300,
                    'name' => 'dtag.test.adslot',
                    'visible' => false
                ],
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
        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/displayadslots/' . $item['id'], [
            'site' => PARAMS_SITE,
            'libraryAdSlot' => [
                'height' => 250
            ]
        ]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * @depends addAdSlot
     */
    public function patchAdSlotMoveToLibrary(ApiTester $I)
    {
        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/displayadslots/' . $item['id'], [
            'libraryAdSlot' => [
                'visible' => true,
                'name' => 'dtag.test.adslot-library'
            ]
        ]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * add adSlot failed caused by name null
     * @param ApiTester $I
     */
    public function patchAdSlotWithNameNull(ApiTester $I)
    {
        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/displayadslots/' . $item['id'],
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
        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/displayadslots/' . $item['id'],
            [
                'libraryAdSlot' => [
                    'width' => '250_wrong',
                    'height' => '250_wrong'
                ]
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
        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/displayadslots/' . $item['id'],
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
        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_API . '/displayadslots/' . $item['id']);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete AdSlot Not Existed
     * @depends addAdSlot
     */
    public function deleteAdSlotNotExisted(ApiTester $I)
    {
        $I->sendDELETE(URL_API . '/displayadslots/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * get AdTags By AdSlot
     * @param ApiTester $I
     */
    public function getAdTagsByAdSlot(ApiTester $I)
    {
        $I->sendGET(URL_API . '/displayadslots/' . PARAMS_AD_SLOT . '/adtags');
        $I->seeResponseCodeIs(200);
    }

    /**
     * get AdTags By AdSlot failed cause by Not Existed
     * @param ApiTester $I
     */
    public function getAdTagsByAdSlotNotExisted(ApiTester $I)
    {
        $I->sendGET(URL_API . '/displayadslots/' . '-1' . '/adtags');
        $I->seeResponseCodeIs(404);
    }

//    public function addPositionsAdSlot(ApiTester $I) {
//        $I->sendPOST(URL_API.'/displayadslots/1/adtags/positions', []);
//        $I->seeResponseCodeIs(201);
//    }

    /**
     * get Js AdTags By AdSlot
     * @param ApiTester $I
     */
    public function getJsAdTagsByAdSlot(ApiTester $I)
    {
        $I->sendGET(URL_API . '/displayadslots/' . PARAMS_AD_SLOT . '/jstag');
        $I->seeResponseCodeIs(200);
    }

    /**
     * get Js AdTags By AdSlot failed cause by Not Existed
     * @param ApiTester $I
     */
    public function getJsAdTagsByAdSlotNotExisted(ApiTester $I)
    {
        $I->sendGET(URL_API . '/displayadslots/' . '-1' . '/jstag');
        $I->seeResponseCodeIs(404);
    }
}