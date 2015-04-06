<?php

/**
 * @group admin
 */
class StatisticsAdminCest extends Statistics
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }

    public function getStatisticsPlatform(ApiTester $I) {
        $I->sendGet(URL_STATISTICS.'/platform?startDate='.START_DATE.'&endDate='.END_DATE);
    }


    public function getStatisticsPlatformProjectedbill(ApiTester $I) {
        $I->sendGet(URL_STATISTICS.'/platform/projectedbill');
    }

    public function getStatisticsPlatformSummary(ApiTester $I) {
        $I->sendGet(URL_STATISTICS.'/platform/summary?startMonth='.START_MONTH.'&endMonth='.END_MONTH);
    }
}