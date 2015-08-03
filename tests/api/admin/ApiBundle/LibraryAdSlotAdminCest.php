<?php

/**
 * @group admin
 */
class LibraryAdSlotAdminCest extends LibraryAdSlot
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }
}