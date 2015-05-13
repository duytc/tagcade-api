<?php

class User
{
    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I)
    {
    }

    /**
     * get All User
     * @param ApiTester $I
     */
    public function getAllUser(ApiTester $I)
    {
        $I->sendGet(URL_ADMIN_API . '/users');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get User By Id
     * @param ApiTester $I
     */
    public function getUserById(ApiTester $I)
    {
        $I->sendGet(URL_ADMIN_API . '/users/' . PARAMS_PUBLISHER);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get User By Id failed cause by not existed
     * @param ApiTester $I
     */
    public function getUserByIdNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_ADMIN_API . '/users/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * get Ad Network By User
     * @param ApiTester $I
     */
    public function getAdNetworkByUser(ApiTester $I)
    {
        $I->sendGet(URL_ADMIN_API . '/users/' . PARAMS_PUBLISHER . '/adnetworks');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get Ad Network By User failed cause by not existed
     * @param ApiTester $I
     */
    public function getAdNetworkByUserNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_ADMIN_API . '/users/' . '-1' . '/adnetworks');
        $I->seeResponseCodeIs(404);
    }

    /**
     * get Site By User
     * @param ApiTester $I
     */
    public function getSiteByUser(ApiTester $I)
    {
        $I->sendGet(URL_ADMIN_API . '/users/' . PARAMS_PUBLISHER . '/sites');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get Site By User failed cause by not existed
     * @param ApiTester $I
     */
    public function getSiteByUserNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_ADMIN_API . '/users/' . '-1' . '/sites');
        $I->seeResponseCodeIs(404);
    }

    /**
     * get Token By User
     * @param ApiTester $I
     */
    public function getTokenByUser(ApiTester $I)
    {
        $I->sendGet(URL_ADMIN_API . '/users/' . PARAMS_PUBLISHER . '/token');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get Token By User failed cause by not existed
     * @param ApiTester $I
     */
    public function getTokenByUserNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_ADMIN_API . '/users/' . '-1' . '/token');
        $I->seeResponseCodeIs(404);
    }

    /**
     * add User
     * @param ApiTester $I
     */
    public function addUser(ApiTester $I)
    {
        $I->sendPost(URL_ADMIN_API . '/users',
            [
                "username" => "api-test-" . date("YmdHis") . '_' . rand(0, 99),
                "plainPassword" => 'api-pass',
                "address" => null,
                "billingRate" => 1,
                "city" => "Ha Noi",
                "company" => "D-TAG Vietnam",
                "country" => "Viet Nam",
                "email" => "api-test-dtag@test.com" . date("YmdHis") . '_' . rand(0, 99),
                "enabled" => true,
                "enabledModules" => ["MODULE_DISPLAY", "MODULE_ANALYTICS"],
                "firstName" => "D-TAG",
                "lastName" => "VIET NAM",
                "phone" => "989403333",
                "postalCode" => "04",
                "state" => ":)"
            ]
        );
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
    }

    /**
     * add User failed cause by null field
     * @param ApiTester $I
     */
    public function addUserWithNullField(ApiTester $I)
    {
        $I->sendPost(URL_ADMIN_API . '/users',
            [
                "username" => null, //this is null field
                "plainPassword" => 'api-pass',
                "address" => null,
                "billingRate" => 1,
                "city" => "Ha Noi",
                "company" => "D-TAG Vietnam",
                "country" => "Viet Nam",
                "email" => "api-test-dtag@test.com" . date("YmdHis") . '_' . rand(0, 99),
                "enabled" => true,
                "enabledModules" => ["MODULE_DISPLAY", "MODULE_ANALYTICS"],
                "firstName" => "D-TAG",
                "lastName" => "VIET NAM",
                "phone" => "989403333",
                "postalCode" => "04",
                "state" => ":)"
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add User failed cause by unexpected field
     * @param ApiTester $I
     */
    public function addUserWithUnexpectedField(ApiTester $I)
    {
        $I->sendPost(URL_ADMIN_API . '/users',
            [
                "username" => "api-test-" . date("YmdHis") . '_' . rand(0, 99),
                "unexpected_field" => "api-test-", //this is unexpected field
                "plainPassword" => 'api-pass',
                "address" => null,
                "billingRate" => 1,
                "city" => "Ha Noi",
                "company" => "D-TAG Vietnam",
                "country" => "Viet Nam",
                "email" => "api-test-dtag@test.com" . date("YmdHis") . '_' . rand(0, 99),
                "enabled" => true,
                "enabledModules" => ["MODULE_DISPLAY", "MODULE_ANALYTICS"],
                "firstName" => "D-TAG",
                "lastName" => "VIET NAM",
                "phone" => "989403333",
                "postalCode" => "04",
                "state" => ":)"
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add User failed cause by missing field
     * @param ApiTester $I
     */
    public function addUserWithMissingField(ApiTester $I)
    {
        $I->sendPost(URL_ADMIN_API . '/users',
            [
                //"username" => "api-test-" . date("YmdHis") . '_' . rand(0, 99), //this is missing field
                "plainPassword" => 'api-pass',
                "address" => null,
                "billingRate" => 1,
                "city" => "Ha Noi",
                "company" => "D-TAG Vietnam",
                "country" => "Viet Nam",
                "email" => "api-test-dtag@test.com" . date("YmdHis") . '_' . rand(0, 99),
                "enabled" => true,
                "enabledModules" => ["MODULE_DISPLAY", "MODULE_ANALYTICS"],
                "firstName" => "D-TAG",
                "lastName" => "VIET NAM",
                "phone" => "989403333",
                "postalCode" => "04",
                "state" => ":)"
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add User failed cause by wrong data
     * @param ApiTester $I
     */
    public function addUserWithWrongData(ApiTester $I)
    {
        $I->sendPost(URL_ADMIN_API . '/users',
            [
                "username" => "api-test-" . date("YmdHis") . '_' . rand(0, 99),
                "plainPassword" => 'api-pass',
                "address" => null,
                "billingRate" => -1, //this is wrong data, must be positive
                "city" => "Ha Noi",
                "company" => "D-TAG Vietnam",
                "country" => "Viet Nam",
                "email" => "api-test-dtag@test.com" . date("YmdHis") . '_' . rand(0, 99),
                "enabled" => true,
                "enabledModules" => ["MODULE_DISPLAY", "MODULE_ANALYTICS"],
                "firstName" => "D-TAG",
                "lastName" => "VIET NAM",
                "phone" => "989403333",
                "postalCode" => "04",
                "state" => ":)"
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * add User failed cause by wrong data type
     * @param ApiTester $I
     */
    public function addUserWithWrongDataType(ApiTester $I)
    {
        $I->sendPost(URL_ADMIN_API . '/users',
            [
                "username" => "api-test-" . date("YmdHis") . '_' . rand(0, 99),
                "plainPassword" => 'api-pass',
                "address" => null,
                "billingRate" => '1_wrong', //this is wrong data type, must be number and positive
                "city" => "Ha Noi",
                "company" => "D-TAG Vietnam",
                "country" => "Viet Nam",
                "email" => "api-test-dtag@test.com" . date("YmdHis") . '_' . rand(0, 99),
                "enabled" => true,
                "enabledModules" => ["MODULE_DISPLAY", "MODULE_ANALYTICS"],
                "firstName" => "D-TAG",
                "lastName" => "VIET NAM",
                "phone" => "989403333",
                "postalCode" => "04",
                "state" => ":)"
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit User
     * @depends addUser
     */
    public function editUser(ApiTester $I)
    {
        $I->sendGet(URL_ADMIN_API . '/users');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPut(URL_ADMIN_API . '/users/' . $item['id'], [
            "username" => "test-put" . date("YmdHis") . '_' . rand(0, 99),
            "plainPassword" => 'test-put',
            "address" => null,
            "billingRate" => 1,
            "city" => "Ha Noi",
            "company" => "D-TAG Vietnam",
            "country" => "Viet Nam",
            "email" => "test-put@test.com" . date("YmdHis") . '_' . rand(0, 99),
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

    /**
     * edit User failed by null field
     * @depends addUser
     */
    public function editUserWithNullField(ApiTester $I)
    {
        $I->sendGet(URL_ADMIN_API . '/users');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPut(URL_ADMIN_API . '/users/' . $item['id'], [
            "username" => null, //this is null field
            "plainPassword" => 'test-put',
            "address" => null,
            "billingRate" => 1,
            "city" => "Ha Noi",
            "company" => "D-TAG Vietnam",
            "country" => "Viet Nam",
            "email" => "test-put@test.com" . date("YmdHis") . '_' . rand(0, 99),
            "enabled" => true,
            "enabledModules" => ["MODULE_DISPLAY", "MODULE_ANALYTICS"],
            "firstName" => "D-TAG",
            "lastName" => "VIET NAM",
            "phone" => "123456789",
            "postalCode" => "04",
            "state" => ":)"
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit User failed by unexpected field
     * @depends addUser
     */
    public function editUserWithUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_ADMIN_API . '/users');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPut(URL_ADMIN_API . '/users/' . $item['id'], [
            "username" => "test-put" . date("YmdHis") . '_' . rand(0, 99),
            "unexpected_field" => 'test', //this is unexpected field
            "plainPassword" => 'test-put',
            "address" => null,
            "billingRate" => 1,
            "city" => "Ha Noi",
            "company" => "D-TAG Vietnam",
            "country" => "Viet Nam",
            "email" => "test-put@test.com" . date("YmdHis") . '_' . rand(0, 99),
            "enabled" => true,
            "enabledModules" => ["MODULE_DISPLAY", "MODULE_ANALYTICS"],
            "firstName" => "D-TAG",
            "lastName" => "VIET NAM",
            "phone" => "123456789",
            "postalCode" => "04",
            "state" => ":)"
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * delete User
     * @depends addUser
     * @param ApiTester $I
     */
    public function deleteUser(ApiTester $I)
    {
        $I->sendGet(URL_ADMIN_API . '/users');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_ADMIN_API . '/users/' . $item['id']);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete User failed cause by not existed
     * @depends addUser
     * @param ApiTester $I
     */
    public function deleteUserNotExisted(ApiTester $I)
    {
        $I->sendDELETE(URL_ADMIN_API . '/users/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * patch User
     * @param ApiTester $I
     */
    public function patchUser(ApiTester $I)
    {
        $I->sendPATCH(URL_ADMIN_API . '/users/' . PARAMS_PUBLISHER,
            [
                "city" => "TP HCM"
            ]
        );
        $I->seeResponseCodeIs(204);
        $I->seeResponseIsJson();
    }

    /**
     * patch User failed by null field
     * @param ApiTester $I
     */
    public function patchUserWithNullField(ApiTester $I)
    {
        $I->sendPATCH(URL_ADMIN_API . '/users/' . PARAMS_PUBLISHER,
            [
                "username" => null
            ]
        );
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch User failed by null field
     * @param ApiTester $I
     */
    public function patchUserWithUnexpectedField(ApiTester $I)
    {
        $I->sendPATCH(URL_ADMIN_API . '/users/' . PARAMS_PUBLISHER,
            [
                "unexpected_field" => 'test',
                "city" => "TP HCM"
            ]
        );
        $I->seeResponseCodeIs(400);
    }
}