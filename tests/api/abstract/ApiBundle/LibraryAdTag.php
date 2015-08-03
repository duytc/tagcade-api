<?php

class LibraryAdTag
{
    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I)
    {
    }

    /**
     * get All LibraryAdTag
     * @param ApiTester $I
     */
    public function getAllLibraryAdTag(ApiTester $I)
    {
        $I->sendGet(URL_API . '/libraryadtags');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get LibraryAdTag By Id
     * @param ApiTester $I
     */
    public function getLibraryAdTagById(ApiTester $I)
    {
        $I->sendGet(URL_API . '/libraryadtags/' . PARAMS_LIBRARY_AD_TAG);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get LibraryAdTag By Id Not Existed
     * @param ApiTester $I
     */
    public function getLibraryAdTagByIdNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/libraryadtags/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * add LibraryAdTag
     * @param ApiTester $I
     */
    public function addLibraryAdTag(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/libraryadtags', [
            'html' => 'oki',
            'adNetwork' => PARAMS_AD_NETWORK,
            'adType' => 1,
            "descriptor" => [
                "imageUrl" => "http://www.libraryAdTag-test.com/image.jpg",
                "targetUrl" => "http://www.libraryAdTag-test.com"
            ],
            'referenceName' => 'libraryAdTag-test',
            'visible' => true,
            'id' => null
        ]);


        $I->seeResponseCodeIs(201);
    }

    /**
     * add LibraryAdTag failed by missing field
     * @param ApiTester $I
     */
    public function addLibraryAdTagMissingField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/libraryadtags', [
            'html' => 'oki',
            'adNetwork' => PARAMS_AD_NETWORK,
            //'adType' => 1, //this field is missing
            "descriptor" => [
                "imageUrl" => "http://www.libraryAdTag-test.com/image.jpg",
                "targetUrl" => "http://www.libraryAdTag-test.com"
            ],
            'referenceName' => 'libraryAdTag-test',
            'visible' => true,
            'id' => null
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryAdTag failed by null field
     * @param ApiTester $I
     */
    public function addLibraryAdTagNullField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/libraryadtags', [
            'html' => 'oki',
            'adNetwork' => PARAMS_AD_NETWORK,
            'adType' => 1,
            "descriptor" => [
                "imageUrl" => "http://www.libraryAdTag-test.com/image.jpg",
                "targetUrl" => "http://www.libraryAdTag-test.com"
            ],
            'referenceName' => null, //this field is null
            'visible' => true,
            'id' => null
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryAdTag failed by wrong data type
     * @param ApiTester $I
     */
    public function addLibraryAdTagWrongDataType(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/libraryadtags', [
            'html' => 'oki',
            'adNetwork' => PARAMS_AD_NETWORK,
            'adType' => '1_wrong', //this is wrong data type
            "descriptor" => [
                "imageUrl" => "http://www.libraryAdTag-test.com/image.jpg",
                "targetUrl" => "http://www.libraryAdTag-test.com"
            ],
            'referenceName' => 'libraryAdTag-test',
            'visible' => true,
            'id' => null
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryAdTag failed by wrong data
     * @param ApiTester $I
     */
    public function addLibraryAdTagWrongData(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/libraryadtags', [
            'html' => 'oki',
            'adNetwork' => PARAMS_AD_NETWORK,
            'adType' => -1, //this is wrong data, must positive
            "descriptor" => [
                "imageUrl" => "http://www.libraryAdTag-test.com/image.jpg",
                "targetUrl" => "http://www.libraryAdTag-test.com"
            ],
            'referenceName' => 'libraryAdTag-test',
            'visible' => true,
            'id' => null
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryAdTag failed by contains unexpected field
     * @param ApiTester $I
     */
    public function addLibraryAdTagWithUnexpectedField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/libraryadtags', [
            'html' => 'oki',
            'adNetwork' => PARAMS_AD_NETWORK,
            'adType' => 1,
            'unexpected_field' => 'libraryAdTag-test', //this is unexpected field
            "descriptor" => [
                "imageUrl" => "http://www.libraryAdTag-test.com/image.jpg",
                "targetUrl" => "http://www.libraryAdTag-test.com"
            ],
            'referenceName' => 'libraryAdTag-test',
            'visible' => true,
            'id' => null
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryAdTag failed by descriptor invalid on Missing field
     * @param ApiTester $I
     */
    public function addLibraryAdTagWithDescriptorInvalidByMissingField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/libraryadtags', [
            'html' => 'oki',
            'adNetwork' => PARAMS_AD_NETWORK,
            'adType' => 1,
            "descriptor" => [
                "imageUrl" => "http://www.libraryAdTag-test.com/image.jpg",
                //"targetUrl" => "http://www.libraryAdTag-test.com" //this is missing field
            ],
            'referenceName' => 'libraryAdTag-test',
            'visible' => true,
            'id' => null
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryAdTag SUCCESSFULLY by descriptor valid although Unexpected field
     * @param ApiTester $I
     */
    public function addLibraryAdTagWithDescriptorValidByUnexpectedField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/libraryadtags', [
            'html' => 'oki',
            'adNetwork' => PARAMS_AD_NETWORK,
            'adType' => 1,
            "descriptor" => [
                "imageUrl" => "http://www.libraryAdTag-test.com/image.jpg",
                "targetUrl" => "http://www.libraryAdTag-test.com",
                "unexpected_field" => "http_unexpected_field.com" //this is unexpected field
            ],
            'referenceName' => 'libraryAdTag-test',
            'visible' => true,
            'id' => null
        ]);
        $I->seeResponseCodeIs(201);
    }

    /**
     * add LibraryAdTag failed by descriptor url invalid
     * @param ApiTester $I
     */
    public function addLibraryAdTagWithDescriptorURLInvalid(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/libraryadtags', [
            'html' => 'oki',
            'adNetwork' => PARAMS_AD_NETWORK,
            'adType' => 1,
            "descriptor" => [
                "imageUrl" => "http_wrong_url/image.jpg", //this is invalid URL
                "targetUrl" => "http_wrong_url.libraryAdTag-test.com" //this is invalid URL
            ],
            'referenceName' => 'libraryAdTag-test',
            'visible' => true,
            'id' => null
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add LibraryAdTag failed by descriptor url invalid on image url endWith
     * @param ApiTester $I
     */
    public function addLibraryAdTagWithDescriptorURLInvalidOnImageUrlEndWith(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/libraryadtags', [
            'html' => 'oki',
            'adNetwork' => PARAMS_AD_NETWORK,
            'adType' => 1,
            "descriptor" => [
                "imageUrl" => "http://www.libraryAdTag-test.com/image.jpg_wrong", //this is invalid image URL
                "targetUrl" => "http://www.libraryAdTag-test.com"
            ],
            'referenceName' => 'libraryAdTag-test',
            'visible' => true,
            'id' => null
        ]);
        //$I->seeResponseCodeIs(400);
        $I->seeResponseCodeIs(201); //now uncheck extension
    }

    /**
     * edit LibraryAdTag
     * @param ApiTester $I
     * @depends addLibraryAdTag
     */
    public function editLibraryAdTag(ApiTester $I)
    {
        $I->sendGet(URL_API . '/libraryadtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/libraryadtags/' . $item['id'], [
            'html' => 'oki',
            'visible' => true,
            'adNetwork' => PARAMS_AD_NETWORK,
            'adType' => 1,
            "descriptor" => [
                "imageUrl" => "http://www.libraryAdTag-test.com/image.jpg",
                "targetUrl" => "http://www.libraryAdTag-test.com"
            ],
            'referenceName' => 'libraryAdTag-test-rename',
            'id' => $item['id']
        ]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * edit LibraryAdTag failed by null field
     * @depends addLibraryAdTag
     * @param ApiTester $I
     */
    public function editLibraryAdTagWithNullField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/libraryadtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/libraryadtags/' . $item['id'], [
            'html' => 'oki',
            'visible' => true,
            'adNetwork' => PARAMS_AD_NETWORK,
            'adType' => 1,
            "descriptor" => [
                "imageUrl" => "http://www.libraryAdTag-test.com/image.jpg",
                "targetUrl" => "http://www.libraryAdTag-test.com"
            ],
            'referenceName' => null, //this is null field
            'id' => $item['id']
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit LibraryAdTag failed by unexpected field
     * @depends addLibraryAdTag
     * @param ApiTester $I
     */
    public function editLibraryAdTagWithUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/libraryadtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/libraryadtags/' . $item['id'], [
            'html' => 'oki',
            'visible' => true,
            'adNetwork' => PARAMS_AD_NETWORK,
            'adType' => 1,
            'unexpected_field' => 'libraryAdTag-test', //this is unexpected field
            "descriptor" => [
                "imageUrl" => "http://www.libraryAdTag-test.com/image.jpg",
                "targetUrl" => "http://www.libraryAdTag-test.com"
            ],
            'referenceName' => 'libraryAdTag-test',
            'id' => $item['id']
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit LibraryAdTag failed by wrong data
     * @depends addLibraryAdTag
     * @param ApiTester $I
     */
    public function editLibraryAdTagWithWrongData(ApiTester $I)
    {
        $I->sendGet(URL_API . '/libraryadtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/libraryadtags/' . $item['id'], [
            'html' => 'oki',
            'visible' => true,
            'adNetwork' => PARAMS_AD_NETWORK,
            'adType' => -1, //this is wrong data, must positive
            "descriptor" => [
                "imageUrl" => "http://www.libraryAdTag-test.com/image.jpg",
                "targetUrl" => "http://www.libraryAdTag-test.com"
            ],
            'referenceName' => 'libraryAdTag-test',
            'id' => $item['id']
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit LibraryAdTag failed by wrong data type
     * @depends addLibraryAdTag
     * @param ApiTester $I
     */
    public function editLibraryAdTagWithWrongDataType(ApiTester $I)
    {
        $I->sendGet(URL_API . '/libraryadtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/libraryadtags/' . $item['id'], [
            'html' => 'oki',
            'visible' => true,
            'adNetwork' => PARAMS_AD_NETWORK,
            'adType' => '1_wrong', //this is wrong data type, must number
            "descriptor" => [
                "imageUrl" => "http://www.libraryAdTag-test.com/image.jpg",
                "targetUrl" => "http://www.libraryAdTag-test.com"
            ],
            'referenceName' => 'libraryAdTag-test',
            'id' => $item['id']
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch LibraryAdTag
     * @depends addLibraryAdTag
     * @param ApiTester $I
     */
    public function patchLibraryAdTag(ApiTester $I)
    {
        $I->sendGet(URL_API . '/libraryadtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/libraryadtags/' . $item['id'], [
            'html' => 'oki-edited',
        ]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * patch LibraryAdTag
     * @depends addLibraryAdTag
     * @param ApiTester $I
     */
    public function patchLibraryAdTagMoveToLibrary(ApiTester $I)
    {
        $I->sendGet(URL_API . '/libraryadtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/libraryadtags/' . $item['id'], [
            'visible' => true
        ]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * patch LibraryAdTag failed by null field
     * @depends addLibraryAdTag
     * @param ApiTester $I
     */
    public function patchLibraryAdTagNullField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/libraryadtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/libraryadtags/' . $item['id'], [
            'referenceName' => null //this is field null
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch LibraryAdTag failed by unexpected field
     * @depends addLibraryAdTag
     * @param ApiTester $I
     */
    public function patchLibraryAdTagWithUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/libraryadtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/libraryadtags/' . $item['id'], [
            'html' => 'oki-edited',
            'unexpected_field' => 'oki' //this is unexpected field
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch LibraryAdTag failed by wrong data
     * @depends addLibraryAdTag
     * @param ApiTester $I
     */
    public function patchLibraryAdTagWithWrongData(ApiTester $I)
    {
        $I->sendGet(URL_API . '/libraryadtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/libraryadtags/' . $item['id'], [
            'adType' => -1 //this is wrong data, must possitive
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch LibraryAdTag failed by wrong data type
     * @depends addLibraryAdTag
     * @param ApiTester $I
     */
    public function patchLibraryAdTagWithWrongDataType(ApiTester $I)
    {
        $I->sendGet(URL_API . '/libraryadtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/libraryadtags/' . $item['id'], [
            'adType' => '1_wrong' //this is wrong data type, must number
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * delete LibraryAdTag
     * @depends addLibraryAdTag
     * @param ApiTester $I
     */
    public function deleteLibraryAdTag(ApiTester $I)
    {
        $I->sendGet(URL_API . '/libraryadtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_API . '/libraryadtags/' . $item['id']);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete LibraryAdTag failed cause by Not Existed
     * @depends addLibraryAdTag
     * @param ApiTester $I
     */
    public function deleteLibraryAdTagNotExisted(ApiTester $I)
    {
        $I->sendDELETE(URL_API . '/libraryadtags/' . '-1');
        $I->seeResponseCodeIs(404);
    }
}