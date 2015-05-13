<?php

class Publisher
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I) {
    }

    /**
     * get Publisher Current
     * @param ApiTester $I
     */
    public function getPublisherCurrent(ApiTester $I) {
        $I->sendGet(URL_PUBLISHER_API.'/publishers/current');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * edit Publisher Current
     * @param ApiTester $I
     */
    public function editPublisherCurrent(ApiTester $I) {
        $I->sendPATCH(URL_PUBLISHER_API.'/publishers/current', [
            "company" => "D-TAG Vietnam"
        ]);
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }

    /**
     * edit Publisher Current
     * @param ApiTester $I
     */
    public function editPublisherCurrentWithNullField(ApiTester $I) {
        $I->sendPATCH(URL_PUBLISHER_API.'/publishers/current', [
            "username" => null, //this is null field
            "company" => "D-TAG Vietnam"
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit Publisher Current
     * @param ApiTester $I
     */
    public function editPublisherCurrentWithUnexpectedField(ApiTester $I) {
        $I->sendPATCH(URL_PUBLISHER_API.'/publishers/current', [
            "unexpected_field" => 'test', //this is unexpected field
            "company" => "D-TAG Vietnam"
        ]);
        $I->seeResponseCodeIs(400);
    }
}