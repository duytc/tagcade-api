<?php

class BillingReport
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I) {
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }

    public function getBillingByPublisher(ApiTester $I) {
        $I->sendPUT(URL_BILLING_REPORT.'/publishers/'.PARAMS_PUBLISHER.'/billedRate?billedRate=20&startDate='.START_DATE.'&endDate='.END_DATE);
    }
}