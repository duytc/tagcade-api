<?php

/**
 * @group admin
 */
class ChannelAdminCest extends Channel
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getAdminToken());

        self::$JSON_DATA_SAMPLE_CHANNEL = [
            'publisher' => PARAMS_PUBLISHER,
            'name' => 'dtag.test.channel',
            'channelSites' => [] //default
        ];
    }
}