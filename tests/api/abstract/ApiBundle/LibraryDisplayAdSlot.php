<?php

class LibraryDisplayAdSlot
{
    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());
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
        $I->sendPOST(URL_API . '/librarydisplayadslots',
            [
                'width' => 200,
                'height' => 300,
                'referenceName' => 'dtag.test.librarydisplayadslot',
                //'visible' => true,
                //'publisher' => 2
            ]
        );
        $I->seeResponseCodeIs(201);
    }

    /**
     * add libraryDisplayAdSlot failed caused by name null
     * @param ApiTester $I
     */
    public function addLibraryDisplayAdSlotWithNameNull(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/librarydisplayadslots',
            [
                'width' => 200,
                'height' => 300,
                'referenceName' => null,
                //'visible' => true,
                //'publisher' => 2
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add libraryDisplayAdSlot failed caused by width or height format wrong
     * @param ApiTester $I
     */
    public function addLibraryDisplayAdSlotWithWidthOrHeightInvalid(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/librarydisplayadslots',
            [
                'width' => '300_invalid',
                'height' => '200_invalid',
                'referenceName' => 'dtag.test.librarydisplayadslot',
                //'visible' => true,
                //'publisher' => 2
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add libraryDisplayAdSlot failed caused by missing field
     * @param ApiTester $I
     */
    public function addLibraryDisplayAdSlotMissingField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/librarydisplayadslots',
            [
                'width' => 200,
                'height' => 300,
                //'referenceName' => 'dtag.test.librarydisplayadslot', //this is missing field
                //'visible' => true,
                //'publisher' => 2
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add libraryDisplayAdSlot failed caused by contains unexpected field
     * @param ApiTester $I
     */
    public function addLibraryDisplayAdSlotWithUnexpectedField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/librarydisplayadslots',
            [
                'width' => 200,
                'height' => 300,
                'referenceName' => 'dtag.test.librarydisplayadslot',
                //'visible' => true,
                //'publisher' => 2,
                'unexpected_field' => 'unexpected_field' //this is unexpected field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * @depends addLibraryDisplayAdSlot
     */
    public function patchLibraryDisplayAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarydisplayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/librarydisplayadslots/' . $item['id'], [
            'height' => 250
        ]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * patch LibraryDisplayAdSlot set visible=false
     * @depends addLibraryDisplayAdSlot
     */
    public function patchLibraryDisplayAdSlotSetInvisible(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarydisplayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/librarydisplayadslots/' . $item['id'], [
            'visible' => false,
            'referenceName' => 'dtag.test.librarydisplayadslot-library'
        ]);
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
        $I->sendGet(URL_API . '/librarydisplayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/librarydisplayadslots/' . $item['id'],
            [
                'referenceName' => null
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add libraryDisplayAdSlot failed caused by width or height format wrong
     * @param ApiTester $I
     */
    public function patchLibraryDisplayAdSlotWithWidthOrHeightInvalid(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarydisplayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/librarydisplayadslots/' . $item['id'],
            [
                'width' => '250_wrong',
                'height' => '250_wrong'
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add libraryDisplayAdSlot failed caused by unexpected field
     * @param ApiTester $I
     */
    public function patchLibraryDisplayAdSlotWithUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarydisplayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/librarydisplayadslots/' . $item['id'],
            [
                'unexpected_field' => 'unexpected_field' //this is unexpected field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * @depends addLibraryDisplayAdSlot
     */
    public function deleteLibraryDisplayAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/librarydisplayadslots');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_API . '/librarydisplayadslots/' . $item['id']);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete LibraryDisplayAdSlot Not Existed
     * @depends addLibraryDisplayAdSlot
     */
    public function deleteLibraryDisplayAdSlotNotExisted(ApiTester $I)
    {
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