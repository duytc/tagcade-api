<?php

/**
 * @group admin
 */
class LibraryAdTagAdminCest extends LibraryAdTag
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }
}