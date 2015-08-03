<?php

/**
 * @group admin
 */
class LibraryNativeAdSlotAdminCest extends LibraryNativeAdSlot
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }
}