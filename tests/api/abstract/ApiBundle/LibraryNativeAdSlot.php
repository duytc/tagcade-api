<?php

class LibraryNativeAdSlot
{
    static $JSON_DATA_SAMPLE_LIBRARY_NATIVE_AD_SLOT = [];

    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());

        self::$JSON_DATA_SAMPLE_LIBRARY_NATIVE_AD_SLOT = [
            'name' => 'dtag.test.librarydisplayadslot',
            //'visible' => true, //default
            //'publisher' => 2
        ];
    }

    public function _after(ApiTester $I)
    {
    }

    /**
     * get All LibraryNativeAdSlot
     * @param ApiTester $I
     */
    public function getAllLibraryNativeAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarynativeadslots');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get LibraryNativeAdSlot By Id
     * @param ApiTester $I
     */
    public function getLibraryNativeAdSlotById(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarynativeadslots/' . PARAMS_LIBRARY_NATIVE_AD_SLOT);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get LibraryNativeAdSlot By Id Not Existed
     * @param ApiTester $I
     */
    public function getLibraryNativeAdSlotByIdNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarynativeadslots/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * add LibraryNativeAdSlot
     * @param ApiTester $I
     */
    public function addLibraryNativeAdSlot(ApiTester $I)
    {
        $I->comment('adding library native AdSlot...');

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_NATIVE_AD_SLOT;

        $I->sendPOST(URL_API . '/librarynativeadslots', $jsonData);
        $I->seeResponseCodeIs(201);
    }

    /**
     * add LibraryNativeAdSlot failed caused by name null
     * @param ApiTester $I
     */
    public function addLibraryNativeAdSlotWithNameNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_NATIVE_AD_SLOT;
        //name null
        $jsonData['name'] = null;

        $I->sendPOST(URL_API . '/librarynativeadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryNativeAdSlot failed caused by missing field
     * @param ApiTester $I
     */
    public function addLibraryNativeAdSlotMissingField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_NATIVE_AD_SLOT;
        //name missing
        unset($jsonData['name']);

        $I->sendPOST(URL_API . '/librarynativeadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryNativeAdSlot failed caused by contains unexpected field
     * @param ApiTester $I
     */
    public function addLibraryNativeAdSlotWithUnexpectedField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_NATIVE_AD_SLOT;
        //this is unexpected field
        $jsonData['unexpected_field'] = 'unexpected_field';

        $I->sendPOST(URL_API . '/librarynativeadslots', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * @param ApiTester $I
     */
    public function patchLibraryNativeAdSlot(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryNativeAdSlot($I);

        $I->sendGet(URL_API . '/librarynativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_NATIVE_AD_SLOT;
        //name patched
        $jsonData['name'] = 'dtag.test.libraryNativeAdSlot-rename';

        $I->sendPATCH(URL_API . '/librarynativeadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(204);
    }

    /**
     * patch Native AdSlot set visible=false
     * @param ApiTester $I
     */
    public function patchLibraryNativeAdSlotSetInvisible(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryNativeAdSlot($I);

        $I->sendGet(URL_API . '/librarynativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_NATIVE_AD_SLOT;
        $jsonData['name'] = 'dtag.test.libraryNativeAdSlot-lib2';
        //visible false
        $jsonData['visible'] = false;

        $I->sendPATCH(URL_API . '/librarynativeadslots/' . $item['id'], $jsonData);
        //$I->seeResponseCodeIs(400); //not allow
        $I->canSeeResponseCodeIs(204); //will be delete this adSlot if has no reference
        //$I->canSeeResponseCodeIs(400); //will be error if has at least one reference
    }

    /**
     * add LibraryNativeAdSlot failed caused by name null
     * @param ApiTester $I
     */
    public function patchLibraryNativeAdSlotWithNameNull(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryNativeAdSlot($I);

        $I->sendGet(URL_API . '/librarynativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_NATIVE_AD_SLOT;
        //name null
        $jsonData['name'] = null;

        $I->sendPATCH(URL_API . '/librarynativeadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryNativeAdSlot failed caused by unexpected field
     * @param ApiTester $I
     */
    public function patchLibraryNativeAdSlotWithUnexpectedField(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryNativeAdSlot($I);

        $I->sendGet(URL_API . '/librarynativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_LIBRARY_NATIVE_AD_SLOT;
        unset($jsonData['name']);
        //this is unexpected field
        $jsonData['unexpected_field'] = 'unexpected_field';

        $I->sendPATCH(URL_API . '/librarynativeadslots/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * @depends addLibraryNativeAdSlot
     */
    public function deleteLibraryNativeAdSlot(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryNativeAdSlot($I);

        $I->sendGet(URL_API . '/librarynativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_API . '/librarynativeadslots/' . $item['id']);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete LibraryNativeAdSlot Not Existed
     * @depends addLibraryNativeAdSlot
     */
    public function deleteLibraryNativeAdSlotNotExisted(ApiTester $I)
    {
        //add new before editing
        $this->addLibraryNativeAdSlot($I);

        $I->sendDELETE(URL_API . '/librarynativeadslots/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * Get those AdSlots which refer to the current AdSlot Library
     * @param ApiTester $I
     */
    public function getAssociatedadslotsAction(ApiTester $I){
        $I->sendGET(URL_API . '/librarynativeadslots/' . PARAMS_LIBRARY_NATIVE_AD_SLOT . '/associatedadslots');
        $I->seeResponseCodeIs(200);
    }

    /**
     * Get those AdSlots which refer to the current AdSlot Library
     * @param ApiTester $I
     */
    public function getAssociatedadslotsActionNotExisted(ApiTester $I){
        $I->sendGET(URL_API . '/librarynativeadslots/' . '-1' . '/associatedadslots');
        $I->seeResponseCodeIs(404);
    }

    /**
     * get AdTags By AdSlot
     * @param ApiTester $I
     */
    public function getAdTagsByAdSlot(ApiTester $I)
    {
        $I->sendGET(URL_API . '/librarynativeadslots/' . PARAMS_LIBRARY_NATIVE_AD_SLOT . '/adtags');
        $I->seeResponseCodeIs(200);
    }

    /**
     * get AdTags By AdSlot failed cause by Not Existed
     * @param ApiTester $I
     */
    public function getAdTagsByAdSlotNotExisted(ApiTester $I)
    {
        $I->sendGET(URL_API . '/librarynativeadslots/' . '-1' . '/adtags');
        $I->seeResponseCodeIs(404);
    }
}