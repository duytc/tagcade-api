<?php

class User
{
    public function _before(ApiTester $I) {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I) {
    }

    public function getAllUser(ApiTester $I) {
        $I->sendGet(URL_ADMIN_API.'/users');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function getUserById(ApiTester $I) {
        $I->sendGet(URL_ADMIN_API.'/users/'.PARAMS_PUBLISHER);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function getAdNetworkByUser(ApiTester $I) {
        $I->sendGet(URL_ADMIN_API.'/users/'.PARAMS_PUBLISHER.'/adnetworks');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function getSiteByUser(ApiTester $I) {
        $I->sendGet(URL_ADMIN_API.'/users/'.PARAMS_PUBLISHER.'/sites');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function getTokenByUser(ApiTester $I) {
        $I->sendGet(URL_ADMIN_API.'/users/'.PARAMS_PUBLISHER.'/token');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function addUser(ApiTester $I) {
        $I->sendPost(URL_ADMIN_API.'/users', [
            "username" => "api-test-".rand(0, 99),
            "plainPassword" => 'api-pass',
            "address" => null,
            "billingRate" => 1,
            "city" => "Ha Noi",
            "company" => "D-TAG Vietnam",
            "country" => "Viet Nam",
            "email" => "api-test@test.com",
            "enabled" => true,
            "enabledModules" => ["MODULE_DISPLAY", "MODULE_ANALYTICS"],
            "firstName" => "D-TAG",
            "lastName" => "VIET NAM",
            "phone" => "989403333",
            "postalCode" => "04",
            "state" => ":)"
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    public function deleteAdSlot(ApiTester $I) {
        $I->sendGet(URL_ADMIN_API.'/users');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_ADMIN_API.'/users/'.$item['id']);
        $I->seeResponseCodeIs(204);
    }

    public function editUser(ApiTester $I) {
        $I->sendPut(URL_ADMIN_API.'/users/'.PARAMS_PUBLISHER, [
            "username" => "mypub",
            "plainPassword" => '123455',
            "address" => null,
            "billingRate" => 1,
            "city" => "Ha Noi",
            "company" => "D-TAG Vietnam",
            "country" => "Viet Nam",
            "email" => "dtag-test@test.com",
            "enabled" => true,
            "enabledModules" => ["MODULE_DISPLAY", "MODULE_ANALYTICS"],
            "firstName" => "D-TAG",
            "lastName" => "VIET NAM",
            "phone" => "123456789",
            "postalCode" => "04",
            "state" => ":)"
        ]);
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }

    public function patchUser(ApiTester $I) {
        $I->sendPATCH(URL_ADMIN_API.'/users/'.PARAMS_PUBLISHER, [
            "city" => "TP HCM"
        ]);
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }
}