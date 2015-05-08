<?php

/**
 * @group admin
 */
class DynamicAdSlotAdminCest extends DynamicAdSlot
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());
    }
}