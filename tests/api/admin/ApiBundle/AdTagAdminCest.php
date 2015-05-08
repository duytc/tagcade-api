<?php

/**
 * @group admin
 */
class AdTagAdminCest extends AdTag
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }
}