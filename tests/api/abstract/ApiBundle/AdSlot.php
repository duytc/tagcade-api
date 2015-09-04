<?php

class AdSlot
{
    static $JSON_DATA_SAMPLE_AD_SLOT = [];

    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());

        self::$JSON_DATA_SAMPLE_AD_SLOT = [
            'site' => PARAMS_SITE,
            'libraryAdSlot' => [
                'width' => 200,
                'height' => 300,
                'name' => 'dtag.test.adslot',
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
        $I->comment('adding AdSlot...');

        $jsonData = self::$JSON_DATA_SAMPLE_AD_SLOT;

        $I->sendPOST(URL_API . '/displayadslots', $jsonData);
        $I->seeResponseCodeIs(201);
    }

    /**
     * add adSlot failed caused by name null
     * @param ApiTester $I
     */
    public function addAdSlotWithLibraryAdSlotNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_AD_SLOT;
        //libraryAdSlot null
        $jsonData['libraryAdSlot'] = null;

        $I->sendPOST(URL_API . '/displayadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by name null
     * @param ApiTester $I
     */
    public function addAdSlotWithNameNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_AD_SLOT;
        //libraryAdSlot name null
        $jsonData['libraryAdSlot']['name'] = null;

        $I->sendPOST(URL_API . '/displayadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by width or height format wrong
     * @param ApiTester $I
     */
    public function addAdSlotWithWidthOrHeightInvalid(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_AD_SLOT;
        //libraryAdSlot width or height invalid
        $jsonData['libraryAdSlot']['width'] = '300_invalid';
        $jsonData['libraryAdSlot']['height'] = '200_invalid';

        $I->sendPOST(URL_API . '/displayadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by missing field
     * @param ApiTester $I
     */
    public function addAdSlotMissingField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_AD_SLOT;
        //libraryAdSlot missing
        unset($jsonData['libraryAdSlot']);

        $I->sendPOST(URL_API . '/displayadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by contains unexpected field
     * @param ApiTester $I
     */
    public function addAdSlotWithUnexpectedField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_AD_SLOT;
        //this is unexpected field
        $jsonData['unexpected_field'] = 'unexpected_field';

        $I->sendPOST(URL_API . '/displayadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * clone adSlot
     * @param ApiTester $I
     */
    public function cloneAdSlot(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_AD_SLOT;
        unset($jsonData['libraryAdSlot']);
        //new name for cloning, replace 'libraryAdSlot' json
        $jsonData['name'] = 'dtag.test.adslot-clone';

        $I->sendPOST(URL_API . '/displayadslots/' . PARAMS_AD_SLOT . '/clone', $jsonData);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    /**
     * clone adSlot failed cause by id not existed
     * @param ApiTester $I
     */
    public function cloneAdSlotNotExisted(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_AD_SLOT;
        unset($jsonData['libraryAdSlot']);
        //new name for cloning, replace 'libraryAdSlot' json
        $jsonData['name'] = 'dtag.test.adslot-clone';

        $I->sendPOST(URL_API . '/displayadslots/' . '-1' . '/clone', $jsonData);
        $I->seeResponseCodeIs(404);
    }

    /**
     * clone adSlot failed caused by null field
     * @param ApiTester $I
     */
    public function cloneAdSlotWithNullField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_AD_SLOT;
        unset($jsonData['libraryAdSlot']);
        //new name for cloning but is NULL, replace 'libraryAdSlot' json
        $jsonData['name'] = null;

        $I->sendPOST(URL_API . '/displayadslots/' . PARAMS_AD_SLOT . '/clone', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * clone adSlot failed cause by wrong data
     * @param ApiTester $I
     */
    public function cloneAdSlotWithWrongData(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_AD_SLOT;
        unset($jsonData['libraryAdSlot']);
        //new name for cloning but is wrong data type field, must not empty, replace 'libraryAdSlot' json
        $jsonData['name'] = "";

        $I->sendPOST(URL_API . '/displayadslots/' . PARAMS_AD_SLOT . '/clone', $jsonData);
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
    public function cloneAdSlotWithMissingField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_AD_SLOT;
        unset($jsonData['libraryAdSlot']);
        //this is unexpected field, require 'name' field
        $jsonData['unexpected_field'] = 'unexpected_field';

        $I->sendPOST(URL_API . '/displayadslots/' . PARAMS_AD_SLOT . '/clone', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * @depends addAdSlot
     */
    public function editAdSlot(ApiTester $I)
    {
        //add new before editing
        $this->addAdSlot($I);

        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_AD_SLOT;
        $jsonData['libraryAdSlot']['name'] = 'dtag.test.adslot-edited';

        $I->sendPUT(URL_API . '/displayadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(204);
    }

    /**
     * add adSlot failed caused by name null
     * @param ApiTester $I
     */
    public function editAdSlotWithLibraryAdSlotNull(ApiTester $I)
    {
        //add new before editing
        $this->addAdSlot($I);

        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_AD_SLOT;
        $jsonData['libraryAdSlot'] = null;

        $I->sendPUT(URL_API . '/displayadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by name null
     * @param ApiTester $I
     */
    public function editAdSlotWithNameNull(ApiTester $I)
    {
        //add new before editing
        $this->addAdSlot($I);

        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_AD_SLOT;
        $jsonData['libraryAdSlot']['name'] = null;

        $I->sendPUT(URL_API . '/displayadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by width or height format wrong
     * @param ApiTester $I
     */
    public function editAdSlotWithWidthOrHeightInvalid(ApiTester $I)
    {
        //add new before editing
        $this->addAdSlot($I);

        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_AD_SLOT;
        $jsonData['libraryAdSlot']['width'] = '300_wrong';
        $jsonData['libraryAdSlot']['height'] = '200_wrong';

        $I->sendPUT(URL_API . '/displayadslots/' . $item['id'], $jsonData);
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

        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_AD_SLOT;
        $jsonData['unexpected_field'] = 'unexpected_field'; //this is unexpected field

        $I->sendPUT(URL_API . '/displayadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * @param ApiTester $I
     */
    public function patchAdSlot(ApiTester $I)
    {
        //add new before editing
        $this->addAdSlot($I);

        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_AD_SLOT;
        $jsonData['libraryAdSlot']['height'] = 250;

        $I->sendPATCH(URL_API . '/displayadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(204);
    }

    /**
     * @param ApiTester $I
     */
    public function patchAdSlotMoveToLibrary(ApiTester $I)
    {
        //add new before editing
        $this->addAdSlot($I);

        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = ['visible' => true];

        $I->sendPATCH(URL_API . '/librarydisplayadslots/' . $item['libraryAdSlot']['id'], $jsonData);
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
        //add new before editing
        $this->addAdSlot($I);

        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_AD_SLOT;
        $jsonData['libraryAdSlot']['width'] = '250_wrong';
        $jsonData['libraryAdSlot']['height'] = '250_wrong';

        $I->sendPATCH(URL_API . '/displayadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add adSlot failed caused by unexpected field
     * @param ApiTester $I
     */
    public function patchAdSlotWithUnexpectedField(ApiTester $I)
    {
        //add new before editing
        $this->addAdSlot($I);

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
     * @param ApiTester $I
     */
    public function deleteAdSlot(ApiTester $I)
    {
        //add new before editing
        $this->addAdSlot($I);

        $I->sendGet(URL_API . '/displayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_API . '/displayadslots/' . $item['id']);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete AdSlot Not Existed
     * @param ApiTester $I
     */
    public function deleteAdSlotNotExisted(ApiTester $I)
    {
        //add new before editing
        $this->addAdSlot($I);

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