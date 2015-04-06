<?php

/**
 * @group admin
 */
class BillingReportAdminCest extends BillingReport
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }
}