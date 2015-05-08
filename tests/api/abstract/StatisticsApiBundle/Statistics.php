<?php

class Statistics
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I) {
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function getStatisticsAccountById(ApiTester $I) {
        $I->sendGet(URL_STATISTICS.'/accounts/'.PARAMS_PUBLISHER.'?startDate='.START_DATE.'&endDate='.END_DATE);
    }

    public function getStatisticsProjectedbillByAccount(ApiTester $I) {
        $I->sendGet(URL_STATISTICS.'/accounts/'.PARAMS_PUBLISHER.'/projectedbill');
    }

    public function getStatisticsProjectedbillSummaryByAccount(ApiTester $I) {
        $I->sendGet(URL_STATISTICS.'/accounts/'.PARAMS_PUBLISHER.'/summary?startMonth='.START_MONTH.'&endMonth='.END_MONTH);
    }

    public function getStatisticsProjectedbillBySite(ApiTester $I) {
        $I->sendGet(URL_STATISTICS.'/sites/'.PARAMS_SITE.'/projectedbill');
    }
}