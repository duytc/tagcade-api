<?php

class NativeAdSlot
{
    static $JSON_DATA_SAMPLE_NATIVE_AD_SLOT = [];

    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());

        self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT = [
            'site' => PARAMS_SITE,
            'libraryAdSlot' => [
                'name' => 'dtag.test.nativeAdslot',
                //'visible' => false //default
            ]
        ];
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
        $I->sendGet(URL_API . '/nativeadslots');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get AdSlot By Id
     * @param ApiTester $I
     */
    public function getAdSlotById(ApiTester $I)
    {
        $I->sendGet(URL_API . '/nativeadslots/' . PARAMS_NATIVE_AD_SLOT);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get AdSlot By Id Not Existed
     * @param ApiTester $I
     */
    public function getAdSlotByIdNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/nativeadslots/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * add AdSlot
     * @param ApiTester $I
     */
    public function addAdSlot(ApiTester $I)
    {
        $I->comment('adding AdSlot...');

        $jsonData = self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT;

        $I->sendPOST(URL_API . '/nativeadslots', $jsonData);
        $I->seeResponseCodeIs(201);
    }

    /**
     * add adSlot failed caused by name null
     * @param ApiTester $I
     */
    public function addAdSlotWithNameNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT;
        //libraryAdSlot name null
        $jsonData['libraryAdSlot']['name'] = null;

        $I->sendPOST(URL_API . '/nativeadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by libraryAdSlot null
     * @param ApiTester $I
     */
    public function addAdSlotWithLibraryAdSlotNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT;
        //libraryAdSlot null
        $jsonData['libraryAdSlot'] = null;

        $I->sendPOST(URL_API . '/nativeadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by missing field
     * @param ApiTester $I
     */
    public function addAdSlotMissingField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT;
        //libraryAdSlot missing
        unset($jsonData['libraryAdSlot']);

        $I->sendPOST(URL_API . '/nativeadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by contains unexpected field
     * @param ApiTester $I
     */
    public function addAdSlotWithUnexpectedField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT;
        //unexpected field
        $jsonData['unexpected_field'] = 'unexpected_field';

        $I->sendPOST(URL_API . '/nativeadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * clone adSlot
     * @param ApiTester $I
     */
    public function cloneAdSlot(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT;
        //unset libraryAdSlot
        unset($jsonData['libraryAdSlot']);
        //new name for cloning, replace 'libraryAdSlot' json
        $jsonData['name'] = 'dtag.test.nativeAdslot-clone';

        $I->sendPOST(URL_API . '/nativeadslots/' . PARAMS_NATIVE_AD_SLOT . '/clone', $jsonData);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    /**
     * clone adSlot failed cause by id not existed
     * @param ApiTester $I
     */
    public function cloneAdSlotNotExisted(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT;
        //unset libraryAdSlot
        unset($jsonData['libraryAdSlot']);
        //new name for cloning, replace 'libraryAdSlot' json
        $jsonData['name'] = 'dtag.test.nativeAdslot-clone';

        $I->sendPOST(URL_API . '/nativeadslots/' . '-1' . '/clone', $jsonData);
        $I->seeResponseCodeIs(404);
    }

    /**
     * clone adSlot failed caused by null field
     * @param ApiTester $I
     */
    public function cloneAdSlotWithNullField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT;
        //unset libraryAdSlot
        unset($jsonData['libraryAdSlot']);
        //new name for cloning, replace 'libraryAdSlot' json
        $jsonData['name'] = null;

        $I->sendPOST(URL_API . '/nativeadslots/' . PARAMS_NATIVE_AD_SLOT . '/clone', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * clone adSlot failed cause by wrong data
     * @param ApiTester $I
     */
    public function cloneAdSlotWithWrongData(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT;
        //unset libraryAdSlot
        unset($jsonData['libraryAdSlot']);
        //new name for cloning, replace 'libraryAdSlot' json
        $jsonData['name'] = '';

        $I->sendPOST(URL_API . '/nativeadslots/' . PARAMS_NATIVE_AD_SLOT . '/clone', $jsonData);
        $I->seeResponseCodeIs(400);
    }

//    /**
//     * clone adSlot failed cause by wrong data type
//     * @param ApiTester $I
//     */
//    public function cloneAdSlotWithWrongDataType(ApiTester $I)
//    {
//        $I->sendPOST(URL_API . '/nativeadslots/' . PARAMS_NATIVE_AD_SLOT . '/clone',
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
        $jsonData = self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT;
        //unset libraryAdSlot
        unset($jsonData['libraryAdSlot']);
        //new name for cloning is missing, replace 'libraryAdSlot' json
        $jsonData['unexpected_field'] = 'unexpected_field';

        $I->sendPOST(URL_API . '/nativeadslots/' . PARAMS_NATIVE_AD_SLOT . '/clone', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * @param ApiTester $I
     */
    public function editAdSlot(ApiTester $I)
    {
        //add new before editing
        $this->addAdSlot($I);

        $I->sendGet(URL_API . '/nativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT;
        $jsonData['libraryAdSlot']['name'] = 'dtag.test.adslot-edited';

        $I->sendPUT(URL_API . '/nativeadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(204);
    }

    /**
     * add adSlot failed caused by name null
     * @param ApiTester $I
     */
    public function editAdSlotWithNameNull(ApiTester $I)
    {
        //add new before editing
        $this->addAdSlot($I);

        $I->sendGet(URL_API . '/nativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT;
        $jsonData['libraryAdSlot']['name'] = null;

        $I->sendPUT(URL_API . '/nativeadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by libraryAdSlot null
     * @param ApiTester $I
     */
    public function editAdSlotWithLibraryAdSlotNull(ApiTester $I)
    {
        //add new before editing
        $this->addAdSlot($I);

        $I->sendGet(URL_API . '/nativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT;
        $jsonData['libraryAdSlot'] = null;

        $I->sendPUT(URL_API . '/nativeadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by unexpected field
     * @param ApiTester $I
     */
    public function editAdSlotWithUnexpectedField(ApiTester $I)
    {
        //add new before editing
        $this->addAdSlot($I);

        $I->sendGet(URL_API . '/nativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT;
        $jsonData['unexpected_field'] = 'unexpected_field';

        $I->sendPUT(URL_API . '/nativeadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * @param ApiTester $I
     */
    public function patchAdSlot(ApiTester $I)
    {
        //add new before editing
        $this->addAdSlot($I);

        $I->sendGet(URL_API . '/nativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT;
        //unset($jsonData['site']); //required!!!
        $jsonData['libraryAdSlot']['name'] = 'dtag.test.adslot-patched';

        $I->sendPATCH(URL_API . '/nativeadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(204);
    }

    /**
     * @param ApiTester $I
     */
    public function patchAdSlotMoveToLibrary(ApiTester $I)
    {
        //add new before editing
        $this->addAdSlot($I);

        $I->sendGet(URL_API . '/nativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = ['visible' => true];

        $I->sendPATCH(URL_API . '/librarynativeadslots/' . $item['libraryAdSlot']['id'], $jsonData);
        $I->seeResponseCodeIs(204);
    }

    /**
     * add adSlot failed caused by name null
     * @param ApiTester $I
     */
    public function patchAdSlotWithNameNull(ApiTester $I)
    {
        //add new before editing
        $this->addAdSlot($I);

        $I->sendGet(URL_API . '/nativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT;
        $jsonData['libraryAdSlot']['name'] = null;

        $I->sendPATCH(URL_API . '/nativeadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by libraryAdSlot null
     * @param ApiTester $I
     */
    public function patchAdSlotWithLibraryAdSlotNull(ApiTester $I)
    {
        //add new before editing
        $this->addAdSlot($I);

        $I->sendGet(URL_API . '/nativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT;
        $jsonData['libraryAdSlot'] = null;

        $I->sendPATCH(URL_API . '/nativeadslots/' . $item['id'],
            [
                'libraryAdSlot' => null
            ]
        );
        $I->seeResponseCodeIs(500); //current, api return in controller, instead of 400 or 404
    }

    /**
     * add adSlot failed caused by unexpected field
     * @param ApiTester $I
     */
    public function patchAdSlotWithUnexpectedField(ApiTester $I)
    {
        //add new before editing
        $this->addAdSlot($I);

        $I->sendGet(URL_API . '/nativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_NATIVE_AD_SLOT;
        $jsonData['unexpected_field'] = 'unexpected_field';

        $I->sendPATCH(URL_API . '/nativeadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * delete ad slot
     * @param ApiTester $I
     */
    public function deleteAdSlot(ApiTester $I)
    {
        //add new before deleting
        $this->addAdSlot($I);

        $I->sendGet(URL_API . '/nativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_API . '/nativeadslots/' . $item['id']);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete AdSlot Not Existed
     * @depends addAdSlot
     */
    public function deleteAdSlotNotExisted(ApiTester $I)
    {
        //add new before deleting
        $this->addAdSlot($I);

        $I->sendDELETE(URL_API . '/nativeadslots/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * get AdTags By AdSlot
     * @param ApiTester $I
     */
    public function getAdTagsByAdSlot(ApiTester $I)
    {
        $I->sendGET(URL_API . '/nativeadslots/' . PARAMS_NATIVE_AD_SLOT . '/adtags');
        $I->seeResponseCodeIs(200);
    }

    /**
     * get AdTags By AdSlot failed cause by Not Existed
     * @param ApiTester $I
     */
    public function getAdTagsByAdSlotNotExisted(ApiTester $I)
    {
        $I->sendGET(URL_API . '/nativeadslots/' . '-1' . '/adtags');
        $I->seeResponseCodeIs(404);
    }

//    public function addPositionsAdSlot(ApiTester $I) {
//        $I->sendPOST(URL_API.'/nativeadslots/1/adtags/positions', []);
//        $I->seeResponseCodeIs(201);
//    }

    /**
     * get Js AdTags By AdSlot
     * @param ApiTester $I
     */
    public function getJsAdTagsByAdSlot(ApiTester $I)
    {
        $I->sendGET(URL_API . '/nativeadslots/' . PARAMS_NATIVE_AD_SLOT . '/jstag');
        $I->seeResponseCodeIs(200);
    }

    /**
     * get Js AdTags By AdSlot failed cause by Not Existed
     * @param ApiTester $I
     */
    public function getJsAdTagsByAdSlotNotExisted(ApiTester $I)
    {
        $I->sendGET(URL_API . '/nativeadslots/' . '-1' . '/jstag');
        $I->seeResponseCodeIs(404);
    }
}