<?php

/**
 * @group admin
 */
class PerformanceReportAdminCest extends PerformanceReport
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }

    public function getPlatform(ApiTester $I) {
        $I->sendGet(URL_PERFORMANCE_REPORT.'/platform?group=true&startDate='.START_DATE.'&endDate='.END_DATE);
    }

    public function getPlatformAccount(ApiTester $I) {
        $I->sendGet(URL_PERFORMANCE_REPORT.'/platform/accounts?group=true&startDate='.START_DATE.'&endDate='.END_DATE);
    }

    public function getPlatformSite(ApiTester $I) {
        $I->sendGet(URL_PERFORMANCE_REPORT.'/platform/sites?group=true&startDate='.START_DATE.'&endDate='.END_DATE);
    }
}