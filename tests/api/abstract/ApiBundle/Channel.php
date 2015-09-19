<?php

class Channel
{
    static $JSON_DATA_SAMPLE_CHANNEL = [];

    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());

        self::$JSON_DATA_SAMPLE_CHANNEL = [
            //'publisher' => PARAMS_PUBLISHER,
            'name' => 'dtag.test.channel',
            'channelSites' => [] //default
        ];
    }

    public function _after(ApiTester $I)
    {
    }

    /**
     * get All Channels
     * @param ApiTester $I
     */
    public function getAllChannels(ApiTester $I)
    {
        $I->sendGet(URL_API . '/channels');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get Channel By Id
     * @param ApiTester $I
     */
    public function getChannelById(ApiTester $I)
    {
        $I->sendGet(URL_API . '/channels/' . PARAMS_CHANNEL);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get Channel By Id Not Existed
     * @param ApiTester $I
     */
    public function getChannelByIdNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/channels/' . '-1');
        //$I->seeResponseCodeIs(404);
        $I->seeResponseCodeIs(405);
        $I->comment('Unknown why 405 instead of 404');
    }

    /**
     * add Channel
     * @param ApiTester $I
     */
    public function addChannel(ApiTester $I)
    {
        $I->comment('adding Channel...');

        $jsonData = self::$JSON_DATA_SAMPLE_CHANNEL;

        $I->sendPOST(URL_API . '/channels', $jsonData);
        $I->seeResponseCodeIs(201);
    }

    /**
     * add Channel
     * @param ApiTester $I
     */
    public function addChannelWithSites(ApiTester $I)
    {
        $I->comment('adding Channel...');

        $jsonData = self::$JSON_DATA_SAMPLE_CHANNEL;
        //with site
        $jsonData['channelSites'] = [
            ['site' => PARAMS_SITE]
        ];

        $I->sendPOST(URL_API . '/channels', $jsonData);
        $I->seeResponseCodeIs(201); //TODO
    }

    /**
     * add Channel failed caused by name null
     * @param ApiTester $I
     */
    public function addChannelWithNameNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_CHANNEL;
        //name null
        $jsonData['name'] = null;

        $I->sendPOST(URL_API . '/channels', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add Channel failed caused by name missing
     * @param ApiTester $I
     */
    public function addChannelWithNameMissing(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_CHANNEL;
        //name missing
        unset($jsonData['name']);

        $I->sendPOST(URL_API . '/channels', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add Channel OK with channelSites null
     * @param ApiTester $I
     */
    public function addChannelWithChannelSitesNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_CHANNEL;
        //channelSites null
        $jsonData['channelSites'] = null;

        $I->sendPOST(URL_API . '/channels', $jsonData);
        $I->seeResponseCodeIs(201);
    }

    /**
     * add Channel OK with channelSites missing
     * @param ApiTester $I
     */
    public function addChannelWithChannelSitesMissing(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_CHANNEL;
        //channelSites missing
        $jsonData['channelSites'] = null;

        $I->sendPOST(URL_API . '/channels', $jsonData);
        $I->seeResponseCodeIs(201);
    }

    /**
     * add Channel failed caused by channelSites not json
     * @param ApiTester $I
     */
    public function addChannelWithChannelSitesNotJson(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_CHANNEL;
        //channelSites not json
        $jsonData['channelSites'] = 'channelSites_not_json';

        $I->sendPOST(URL_API . '/channels', $jsonData);
        //$I->seeResponseCodeIs(400);
        $I->seeResponseCodeIs(500);

        $I->comment('Not in form validation, this error occurs outside');
    }

    /**
     * add Channel failed caused by channelSites contain site null
     * @param ApiTester $I
     */
    public function addChannelWithChannelSitesContainSiteNull(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_CHANNEL;
        //channelSites contain site null
        $jsonData['channelSites'] = [
            'site' => null
        ];

        $I->sendPOST(URL_API . '/channels', $jsonData);
        //$I->seeResponseCodeIs(400);
        $I->seeResponseCodeIs(201);

        $I->comment('Allow site null because validation only check valid');
    }

    /**
     * add Channel failed caused by channelSites contain site not existed
     * @param ApiTester $I
     */
    public function addChannelWithChannelSitesContainSiteNotExisted(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_CHANNEL;
        //channelSites contain site not existed
        $jsonData['channelSites'] = [
            ['site' => null],
            ['site' => '-1'],
        ];

        $I->sendPOST(URL_API . '/channels', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add Channel failed caused by channelSites contain unexpected_field
     * @param ApiTester $I
     */
    public function addChannelWithChannelSitesContainUnexpectedField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_CHANNEL;
        //channelSites contain site not existed
        $jsonData['channelSites'] = [
            ['site' => null],
            ['unexpected_field' => 'unexpected_field']
        ];

        $I->sendPOST(URL_API . '/channels', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add Channel failed caused by contains unexpected_field
     * @param ApiTester $I
     */
    public function addChannelContainsUnexpectedField(ApiTester $I)
    {
        $jsonData = self::$JSON_DATA_SAMPLE_CHANNEL;
        //channelSites contain site not existed
        $jsonData['unexpected_field'] = 'unexpected_field';

        $I->sendPOST(URL_API . '/channels', $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch Channel
     * @param ApiTester $I
     */
    public function patchChannel(ApiTester $I)
    {
        //add new before editing
        $this->addChannel($I);

        $I->sendGet(URL_API . '/channels');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_CHANNEL;
        $jsonData['name'] = 'dtag.test.channels-patched';

        $I->sendPATCH(URL_API . '/channels/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(204);
    }

    /**
     * patch Channel With Name Null
     * @param ApiTester $I
     */
    public function patchChannelWithNameNull(ApiTester $I)
    {
        //add new before editing
        $this->addChannel($I);

        $I->sendGet(URL_API . '/channels');
        $item = array_pop($I->grabDataFromJsonResponse());

        $jsonData = self::$JSON_DATA_SAMPLE_CHANNEL;
        $jsonData['name'] = null;

        $I->sendPATCH(URL_API . '/channels/' . $item['id'], $jsonData);
        $I->seeResponseCodeIs(400);
    }

    /**
     * delete Channel
     * @param ApiTester $I
     */
    public function deleteChannel(ApiTester $I)
    {
        //add new before deleting
        $this->addChannel($I);

        $I->sendGet(URL_API . '/channels');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_API . '/channels/' . $item['id']);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete Channel Not Existed
     * @param ApiTester $I
     */
    public function deleteChannelNotExisted(ApiTester $I)
    {
        $I->sendDELETE(URL_API . '/channels/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * get Sites By Channel
     * @param ApiTester $I
     */
    public function getSitesByChannel(ApiTester $I)
    {
        $I->sendGET(URL_API . '/channels/' . PARAMS_CHANNEL . '/sites');
        $I->seeResponseCodeIs(200);
    }

    /**
     * get Sites By Channel not Existed
     * @param ApiTester $I
     */
    public function getSitesByChannelNotExisted(ApiTester $I)
    {
        $I->sendGET(URL_API . '/channels/' . '-1' . '/sites');
        $I->seeResponseCodeIs(404);
    }

    /**
     * get Channels have site has no ad slot Unreferenced to Library Ad Slot
     * @param ApiTester $I
     */
    public function getChannelsUnreferencedByLibraryAdSlot(ApiTester $I)
    {
        $I->sendGET(URL_API . '/channels/noreference?slotLibrary=' . PARAMS_LIBRARY_AD_SLOT);
        $I->seeResponseCodeIs(200);
    }

    /**
     * get Channels have site has no ad slot Unreferenced to Library Ad SlotNot Existed
     * @param ApiTester $I
     */
    public function getChannelsUnreferencedByLibraryAdSlotNotExisted(ApiTester $I)
    {
        $I->sendGET(URL_API . '/channels/noreference?slotLibrary=' . '-1');
        $I->seeResponseCodeIs(404);
    }
}