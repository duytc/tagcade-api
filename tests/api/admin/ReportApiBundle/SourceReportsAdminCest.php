<?php

/**
 * @group admin
 */
class SourceReportAdminCest extends SourceReport
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }
}