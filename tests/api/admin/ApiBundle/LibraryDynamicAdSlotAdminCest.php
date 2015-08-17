<?php

/**
 * @group admin
 */
class LibraryDynamicAdSlotAdminCest extends LibraryDynamicAdSlot
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }
}