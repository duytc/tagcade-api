<?php

/**
 * @group admin
 */
class UserAdminCest extends User
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }
}