<?php

class LibraryNativeAdSlot
{
    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());
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
        $I->sendPOST(URL_API . '/librarynativeadslots',
            [
                //'publisher' => 2,
                //'visible' => true,
                'name' => 'dtag.test.libraryNativeAdSlot'
            ]
        );
        $I->seeResponseCodeIs(201);
    }

    /**
     * add LibraryNativeAdSlot failed caused by name null
     * @param ApiTester $I
     */
    public function addLibraryNativeAdSlotWithNameNull(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/librarynativeadslots',
            [
                //'publisher' => 2,
                //'visible' => true,
                'name' => null
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryNativeAdSlot failed caused by missing field
     * @param ApiTester $I
     */
    public function addLibraryNativeAdSlotMissingField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/librarynativeadslots',
            [
                //'publisher' => 2,
                //'visible' => true
                //'name' => 'dtag.test.libraryNativeAdSlot' //this is missing field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryNativeAdSlot failed caused by contains unexpected field
     * @param ApiTester $I
     */
    public function addLibraryNativeAdSlotWithUnexpectedField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/librarynativeadslots',
            [
                //'publisher' => 2,
                //'visible' => true,
                'unexpected_field' => 'unexpected_field', //this is unexpected field
                'name' => 'dtag.test.libraryNativeAdSlot'
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * @depends addLibraryNativeAdSlot
     */
    public function patchLibraryNativeAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarynativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/librarynativeadslots/' . $item['id'], [
            'name' => 'dtag.test.libraryNativeAdSlot-rename',
        ]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * patch Native AdSlot set visible=false
     * @depends addLibraryNativeAdSlot
     */
    public function patchLibraryNativeAdSlotSetInvisible(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarynativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/librarynativeadslots/' . $item['id'], [
            'name' => 'dtag.test.libraryNativeAdSlot-lib2',
            'visible' => false
        ]);
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
        $I->sendGet(URL_API . '/librarynativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/librarynativeadslots/' . $item['id'],
            [
                'name' => null,
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryNativeAdSlot failed caused by unexpected field
     * @param ApiTester $I
     */
    public function patchLibraryNativeAdSlotWithUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarynativeadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/librarynativeadslots/' . $item['id'],
            [
                'unexpected_field' => 'unexpected_field' //this is unexpected field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * @depends addLibraryNativeAdSlot
     */
    public function deleteLibraryNativeAdSlot(ApiTester $I)
    {
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