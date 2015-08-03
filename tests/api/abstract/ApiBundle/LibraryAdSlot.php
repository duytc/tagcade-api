<?php

class LibraryAdSlot
{
    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I)
    {
    }

    /**
     * get All LibraryAdSlot
     * @param ApiTester $I
     */
    public function getAllLibraryAdSlot(ApiTester $I)
    {
        $I->sendGet(URL_API . '/libraryadslots');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get LibraryAdSlot By Id
     * @param ApiTester $I
     */
    public function getLibraryAdSlotById(ApiTester $I)
    {
        $I->sendGet(URL_API . '/libraryadslots/' . PARAMS_LIBRARY_AD_SLOT);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get LibraryAdSlot By Id Not Existed
     * @param ApiTester $I
     */
    public function getLibraryAdSlotByIdNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/libraryadslots/' . '-1');
        $I->seeResponseCodeIs(404);
    }
}