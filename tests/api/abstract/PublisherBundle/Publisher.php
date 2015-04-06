<?php

class Publisher
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I) {
    }

    public function getPublisherCurrent(ApiTester $I) {
        $I->sendGet(URL_PUBLISHER_API.'/publishers/current');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function editPublisherCurrent(ApiTester $I) {
        $I->sendPATCH(URL_PUBLISHER_API.'/publishers/current', [
            "company" => "D-TAG Vietnam"
        ]);
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }
}