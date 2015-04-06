<?php

class Token
{
    public function _before(ApiTester $I)
    {
    }

    public function _after(ApiTester $I)
    {
        $I->grabDataFromResponseByJsonPath('token');
        $I->seeResponseIsJson();
    }

}