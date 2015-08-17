<?php

/**
 * @group admin
 */
class LibraryDisplayAdSlotAdminCest extends LibraryDisplayAdSlot
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }
}