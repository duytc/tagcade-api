<?php

class SourceReport
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I) {
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function getSourceReportBySite(ApiTester $I) {
        $I->sendGet(URL_SOURCE_REPORT.'/'.PARAMS_SITE.'?rowLimit=30&rowOffset=0&startDate='.START_DATE.'&endDate='.END_DATE);
    }
}