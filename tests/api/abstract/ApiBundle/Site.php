<?php

class Site
{
    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I)
    {
    }

    /**
     * get all site
     * @param ApiTester $I
     */
    public function getAllSite(ApiTester $I)
    {
        $I->sendGet(URL_API . '/sites');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get Site By Id
     * @param ApiTester $I
     */
    public function getSiteById(ApiTester $I)
    {
        $I->sendGet(URL_API . '/sites/' . PARAMS_SITE);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get Site By Id failed cause by Not Existed
     * @param ApiTester $I
     */
    public function getSiteByIdNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/sites/' . PARAMS_SITE);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * patch Site
     * @depends addSite
     */
    public function patchSite(ApiTester $I)
    {
        $I->sendGet(URL_API . '/sites');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/sites/' . $item['id'],
            [
                'name' => 'Dtag.dev1'
            ]
        );
        $I->seeResponseCodeIs(204);
    }

    /**
     * patch Site failed by field null
     * @depends addSite
     */
    public function patchSiteFailedByFieldNull(ApiTester $I)
    {
        $I->sendGet(URL_API . '/sites');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/sites/' . $item['id'],
            [
                'name' => null //this field null
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch Site failed by unexpected field
     * @depends addSite
     */
    public function patchSiteFailedByUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/sites');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/sites/' . $item['id'],
            [
                'name' => 'Dtag.dev1',
                'unexpected_field' => 'Dtag.dev1' //this is unexpected field
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * delete Site
     * @depends addSite
     */
    public function deleteSite(ApiTester $I)
    {
        $I->sendGet(URL_API . '/sites');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_API . '/sites/' . $item['id']);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete Site failed by not existed
     * @depends addSite
     */
    public function deleteSiteNotExisted(ApiTester $I)
    {
        $I->sendDELETE(URL_API . '/sites/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * get AdSlots By Site
     * @param ApiTester $I
     */
    public function getAdSlotsBySite(ApiTester $I)
    {
        $I->sendGET(URL_API . '/sites/' . PARAMS_SITE . '/adslots');
        $I->seeResponseCodeIs(200);
    }

    /**
     * get AdSlots By Site failed cause by site not existed
     * @param ApiTester $I
     */
    public function getAdSlotsBySiteNotExisted(ApiTester $I)
    {
        $I->sendGET(URL_API . '/sites/' . '-1' . '/adslots');
        $I->seeResponseCodeIs(404);
    }

    /**
     * get AdTags Active By Site
     * @param ApiTester $I
     */
    public function getAdTagsActiveBySite(ApiTester $I)
    {
        $I->sendGET(URL_API . '/sites/' . PARAMS_SITE . '/adtags/active');
        $I->seeResponseCodeIs(200);
    }

    /**
     * get AdTags Active By Site failed cause by site not existed
     * @param ApiTester $I
     */
    public function getAdTagsActiveBySiteNotExisted(ApiTester $I)
    {
        $I->sendGET(URL_API . '/sites/' . '-1' . '/adtags/active');
        $I->seeResponseCodeIs(404);
    }

    /**
     * get JsTags By Site
     * @param ApiTester $I
     */
    public function getJsTagsBySite(ApiTester $I)
    {
        $I->sendGET(URL_API . '/sites/' . PARAMS_SITE . '/jstags');
        $I->seeResponseCodeIs(200);
    }

    /**
     * get JsTags By Site failed cause by site not existed
     * @param ApiTester $I
     */
    public function getJsTagsBySiteNotExisted(ApiTester $I)
    {
        $I->sendGET(URL_API . '/sites/' . '-1' . '/jstags');
        $I->seeResponseCodeIs(404);
    }

    /**
     * get Channels By Site
     * @param ApiTester $I
     */
    public function getChannelsBySite(ApiTester $I)
    {
        $I->sendGET(URL_API . '/sites/' . PARAMS_SITE . '/channels');
        $I->seeResponseCodeIs(200);
    }

    /**
     * get Channels By Site not Existed
     * @param ApiTester $I
     */
    public function getChannelsBySiteNotExisted(ApiTester $I)
    {
        $I->sendGET(URL_API . '/sites/' . '-1' . '/channels');
        $I->seeResponseCodeIs(404);
    }

    /**
     * get Sites have no ad slot Unreferenced to Library Ad Slot
     * @param ApiTester $I
     */
    public function getSitesUnreferencedByLibraryAdSlot(ApiTester $I)
    {
        $I->sendGET(URL_API . '/sites/noreference?slotLibrary=' . PARAMS_LIBRARY_AD_SLOT);
        $I->seeResponseCodeIs(200);
    }

    /**
     * get Sites have no ad slot Unreferenced to Library Ad Slot Not Existed
     * @param ApiTester $I
     */
    public function getSitesUnreferencedByLibraryAdSlotNotExisted(ApiTester $I)
    {
        $I->sendGET(URL_API . '/sites/noreference?slotLibrary=' . '-1');
        $I->seeResponseCodeIs(404);
    }
}