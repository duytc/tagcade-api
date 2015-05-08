<?php

/**
 * @group admin
 */
class ActionLogsAdminCest extends ActionLog
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }
}