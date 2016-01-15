<?php

class LibraryDisplayAdSlot
{
    static $JSON_DATA_SAMPLE_LIBRARY_AD_SLOT = [];

    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());

        self::$JSON_DATA_SAMPLE_LIBRARY_AD_SLOT = [
            'width' => 200,
            'height' => 300,
            'name' => 'dtag.test.librarydisplayadslot',
            'autoFit' => true,
            'passbackMode' => 'position'
            //'visible' => true, //default
            //'publisher' => 2
        ];
    }

    public function _after(ApiTester $I)
    {
    }

    /**
     * get All LibraryDisplayAdSlot
     * @param ApiTester $I
     */
    public function getAllLibraryDisplayAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarydisplayadslots');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get LibraryDisplayAdSlot By Id
     * @param ApiTester $I
     */
    public function getLibraryDisplayAdSlotById(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarydisplayadslots/' . PARAMS_LIBRARY_DISPLAY_AD_SLOT);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get LibraryDisplayAdSlot By Id Not Existed
     * @param ApiTester $I
     */
    public function getLibraryDisplayAdSlotByIdNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarydisplayadslots/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * add LibraryDisplayAdSlot
     * @param ApiTester $I
     */
    public function addLibraryDisplayAdSlot(ApiTester $I)
    {
        $I->comment('adding library AdSlot...');

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_AD_SLOT;

        $I->sendPOST(URL_API . '/librarydisplayadslots', $jsonData);
        $I->seeResponseCodeIs(201);
    }

    /**
     * add libraryDisplayAdSlot failed caused by name null
     * @param ApiTester $I
     */
    public function addLibraryDisplayAdSlotWithNameNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_AD_SLOT;
        //name null
        $jsonData['name'] = null;

        $I->sendPOST(URL_API . '/librarydisplayadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add libraryDisplayAdSlot failed caused by width or height format wrong
     * @param ApiTester $I
     */
    public function addLibraryDisplayAdSlotWithWidthOrHeightInvalid(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_AD_SLOT;
        //width, height invalid
        $jsonData['width'] = '300_invalid';
        $jsonData['height'] = '200_invalid';

        $I->sendPOST(URL_API . '/librarydisplayadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add libraryDisplayAdSlot failed caused by missing field
     * @param ApiTester $I
     */
    public function addLibraryDisplayAdSlotMissingField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_AD_SLOT;
        //name missing
        unset($jsonData['name']);

        $I->sendPOST(URL_API . '/librarydisplayadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add libraryDisplayAdSlot failed caused by contains unexpected field
     * @param ApiTester $I
     */
    public function addLibraryDisplayAdSlotWithUnexpectedField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_AD_SLOT;
        //this is unexpected field
        $jsonData['unexpected_field'] = 'unexpected_field';

        $I->sendPOST(URL_API . '/librarydisplayadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * @param ApiTester $I
     */
    public function patchLibraryDisplayAdSlot(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryDisplayAdSlot($I);

        $I->sendGet(URL_API . '/librarydisplayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_AD_SLOT;
        unset($jsonData['name']);
        unset($jsonData['width']);
        //patch height
        $jsonData['height'] = 250;

        $I->sendPATCH(URL_API . '/librarydisplayadslots/' . $item['id'], [
        ]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * patch LibraryDisplayAdSlot set visible=false
     * @depends addLibraryDisplayAdSlot
     */
    public function patchLibraryDisplayAdSlotSetInvisible(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryDisplayAdSlot($I);

        $I->sendGet(URL_API . '/librarydisplayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_AD_SLOT;
        unset($jsonData['width']);
        unset($jsonData['height']);
        //patch height
        $jsonData['name'] = 'dtag.test.librarydisplayadslot-library';
        $jsonData['visible'] = false;

        $I->sendPATCH(URL_API . '/librarydisplayadslots/' . $item['id'], $jsonData);
        //$I->seeResponseCodeIs(400); //not allow
        $I->canSeeResponseCodeIs(204); //will be delete this adSlot if has no reference
        //$I->canSeeResponseCodeIs(400); //will be error if has at least one reference
    }

    /**
     * add libraryDisplayAdSlot failed caused by name null
     * @param ApiTester $I
     */
    public function patchLibraryDisplayAdSlotWithNameNull(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryDisplayAdSlot($I);

        $I->sendGet(URL_API . '/librarydisplayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_AD_SLOT;
        unset($jsonData['width']);
        unset($jsonData['height']);
        //patch name null
        $jsonData['name'] = null;

        $I->sendPATCH(URL_API . '/librarydisplayadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add libraryDisplayAdSlot failed caused by width or height format wrong
     * @param ApiTester $I
     */
    public function patchLibraryDisplayAdSlotWithWidthOrHeightInvalid(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryDisplayAdSlot($I);

        $I->sendGet(URL_API . '/librarydisplayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_AD_SLOT;
        unset($jsonData['name']);
        //patch width, height invalid
        $jsonData['width'] = '250_wrong';
        $jsonData['height'] = '250_wrong';

        $I->sendPATCH(URL_API . '/librarydisplayadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add libraryDisplayAdSlot failed caused by unexpected field
     * @param ApiTester $I
     */
    public function patchLibraryDisplayAdSlotWithUnexpectedField(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryDisplayAdSlot($I);

        $I->sendGet(URL_API . '/librarydisplayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_AD_SLOT;
        unset($jsonData['name']);
        unset($jsonData['width']);
        unset($jsonData['height']);
        //this is unexpected field
        $jsonData['unexpected_field'] = 'unexpected_field';

        $I->sendPATCH(URL_API . '/librarydisplayadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * @param ApiTester $I
     */
    public function deleteLibraryDisplayAdSlot(ApiTester $I)
    {
        //add new before deleting
        $this->addLibraryDisplayAdSlot($I);

        $I->sendGet(URL_API . '/librarydisplayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_API . '/librarydisplayadslots/' . $item['id']);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete LibraryDisplayAdSlot Not Existed
     * @param ApiTester $I
     */
    public function deleteLibraryDisplayAdSlotNotExisted(ApiTester $I)
    {
        //add new before deleting
        $this->addLibraryDisplayAdSlot($I);

        $I->sendDELETE(URL_API . '/librarydisplayadslots/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * Get those AdSlots which refer to the current AdSlot Library
     * @param ApiTester $I
     */
    public function getAssociatedadslotsAction(ApiTester $I){
        $I->sendGET(URL_API . '/librarydisplayadslots/' . PARAMS_LIBRARY_DISPLAY_AD_SLOT . '/associatedadslots');
        $I->seeResponseCodeIs(200);
    }

    /**
     * Get those AdSlots which refer to the current AdSlot Library
     * @param ApiTester $I
     */
    public function getAssociatedadslotsActionNotExisted(ApiTester $I){
        $I->sendGET(URL_API . '/librarydisplayadslots/' . '-1' . '/associatedadslots');
        $I->seeResponseCodeIs(404);
    }

    /**
     * get AdTags By LibraryDisplayAdSlot
     * @param ApiTester $I
     */
    public function getAdTagsByLibraryDisplayAdSlot(ApiTester $I)
    {
        $I->sendGET(URL_API . '/librarydisplayadslots/' . PARAMS_LIBRARY_DISPLAY_AD_SLOT . '/adtags');
        $I->seeResponseCodeIs(200);
    }

    /**
     * get AdTags By LibraryDisplayAdSlot failed cause by Not Existed
     * @param ApiTester $I
     */
    public function getAdTagsByLibraryDisplayAdSlotNotExisted(ApiTester $I)
    {
        $I->sendGET(URL_API . '/librarydisplayadslots/' . '-1' . '/adtags');
        $I->seeResponseCodeIs(404);
    }
}