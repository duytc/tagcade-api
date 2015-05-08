<?php

class ActionLog
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I) {
    }

    public function getActionLogs(ApiTester $I) {
        $I->sendGet(URL_ADMIN_API.'/logs?rowOffset=50&loginLogs=true&publisherId='.PARAMS_PUBLISHER.'&statDate='.START_DATE.'&endDate='.END_DATE);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
}