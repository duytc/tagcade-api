<?php

class AdTag
{
    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I)
    {
    }

    /**
     * get All WaterfallTag
     * @param ApiTester $I
     */
    public function getAllAdTag(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtags');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get WaterfallTag By Id
     * @param ApiTester $I
     */
    public function getAdTagById(ApiTester $I) {
        $I->sendGet(URL_API.'/adtags/'.PARAMS_AD_TAG);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get WaterfallTag By Id Not Existed
     * @param ApiTester $I
     */
    public function getAdTagByIdNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtags/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * add WaterfallTag
     * @param ApiTester $I
     */
    public function addAdTag(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adtags', [
            'adSlot' => PARAMS_AD_SLOT,
            'libraryAdTag' => [
                'html' => 'oki',
                'adNetwork' => PARAMS_AD_NETWORK,
                'adType' => 1,
                "descriptor" => [
                    "imageUrl" => "http://www.adTag-test.com/image.jpg",
                    "targetUrl" => "http://www.adTag-test.com"
                ],
                'name' => 'adTag-test'
            ],
            'frequencyCap' => 300,
            'position' => 6,
            'active' => true,
            "rotation" => 50
        ]);


        $I->seeResponseCodeIs(201);
    }

    /**
     * add WaterfallTag failed by missing field
     * @param ApiTester $I
     */
    public function addAdTagMissingField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adtags', [
            'adSlot' => PARAMS_AD_SLOT,
            //'libraryAdTag' => [
            //    'html' => 'oki',
            //    'adNetwork' => PARAMS_AD_NETWORK,
            //    'adType' => 1,
            //    "descriptor" => [
            //        "imageUrl" => "http://www.adTag-test.com/image.jpg",
            //        "targetUrl" => "http://www.adTag-test.com"
            //    ],
            //    'name' => 'adTag-test'
            //], //this field is missing
            'frequencyCap' => 300,
            'position' => 6,
            'active' => true,
            "rotation" => 50
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add WaterfallTag failed by null field
     * @param ApiTester $I
     */
    public function addAdTagNullField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adtags', [
            'adSlot' => PARAMS_AD_SLOT,
            'libraryAdTag' => null, //this field is null
            'frequencyCap' => 300,
            'position' => 6,
            'active' => true,
            "rotation" => 50
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add WaterfallTag failed by wrong data type
     * @param ApiTester $I
     */
    public function addAdTagWrongDataType(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adtags', [
            'adSlot' => PARAMS_AD_SLOT,
            'libraryAdTag' => [
                'html' => 'oki',
                'adNetwork' => PARAMS_AD_NETWORK,
                'adType' => 1,
                "descriptor" => [
                    "imageUrl" => "http://www.adTag-test.com/image.jpg",
                    "targetUrl" => "http://www.adTag-test.com"
                ],
                'name' => 'adTag-test'
            ],
            'frequencyCap' => '300_wrong', //this is wrong data type
            'position' => 6,
            'active' => true,
            "rotation" => 50
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add WaterfallTag failed by wrong data
     * @param ApiTester $I
     */
    public function addAdTagWrongData(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adtags', [
            'adSlot' => PARAMS_AD_SLOT,
            'libraryAdTag' => [
                'html' => 'oki',
                'adNetwork' => PARAMS_AD_NETWORK,
                'adType' => 1,
                "descriptor" => [
                    "imageUrl" => "http://www.adTag-test.com/image.jpg",
                    "targetUrl" => "http://www.adTag-test.com"
                ],
                'name' => 'adTag-test'
            ],
            'frequencyCap' => -300, //this is wrong data, must positive
            'position' => 6,
            'active' => true,
            "rotation" => 50
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add WaterfallTag failed by contains unexpected field
     * @param ApiTester $I
     */
    public function addAdTagWithUnexpectedField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adtags', [
            'adSlot' => PARAMS_AD_SLOT,
            'unexpected_field' => 'adTag-test', //this is unexpected field
            'libraryAdTag' => [
                'html' => 'oki',
                'adNetwork' => PARAMS_AD_NETWORK,
                'adType' => 1,
                "descriptor" => [
                    "imageUrl" => "http://www.adTag-test.com/image.jpg",
                    "targetUrl" => "http://www.adTag-test.com"
                ],
                'name' => 'adTag-test'
            ],
            'frequencyCap' => 300,
            'position' => 6,
            'active' => true,
            "rotation" => 50
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add WaterfallTag failed by descriptor invalid on Missing field
     * @param ApiTester $I
     */
    public function addAdTagWithDescriptorInvalidByMissingField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adtags', [
            'adSlot' => PARAMS_AD_SLOT,
            'libraryAdTag' => [
                'html' => 'oki',
                'adNetwork' => PARAMS_AD_NETWORK,
                'adType' => 1,
                "descriptor" => [
                    "imageUrl" => "http://www.adTag-test.com/image.jpg",
                    //"targetUrl" => "http://www.adTag-test.com" //this is missing field
                ],
                'name' => 'adTag-test'
            ],
            'frequencyCap' => 300,
            'position' => 6,
            'active' => true,
            "rotation" => 50
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * add WaterfallTag SUCCESSFULLY by descriptor valid although Unexpected field
     * @param ApiTester $I
     */
    public function addAdTagWithDescriptorValidByUnexpectedField(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adtags', [
            'adSlot' => PARAMS_AD_SLOT,
            'libraryAdTag' => [
                'html' => 'oki',
                'adNetwork' => PARAMS_AD_NETWORK,
                'adType' => 1,
                "descriptor" => [
                    "imageUrl" => "http://www.adTag-test.com/image.jpg",
                    "targetUrl" => "http://www.adTag-test.com",
                    "unexpected_field" => "http_unexpected_field.com" //this is unexpected field
                ],
                'name' => 'adTag-test'
            ],
            'frequencyCap' => 300,
            'position' => 6,
            'active' => true,
            "rotation" => 50
        ]);
        $I->seeResponseCodeIs(201);
    }

    /**
     * add WaterfallTag failed by descriptor url invalid
     * @param ApiTester $I
     */
    public function addAdTagWithDescriptorURLInvalid(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adtags', [
            'adSlot' => PARAMS_AD_SLOT,
            'libraryAdTag' => [
                'html' => 'oki',
                'adNetwork' => PARAMS_AD_NETWORK,
                'adType' => 1,
                "descriptor" => [
                    "imageUrl" => "http_wrong_url/image.jpg", //this is invalid URL
                    "targetUrl" => "http_wrong_url.adTag-test.com" //this is invalid URL
                ],
                'name' => 'adTag-test'
            ],
            'frequencyCap' => 300,
            'position' => 6,
            'active' => true,
            "rotation" => 50
        ]);
        $I->seeResponseCodeIs(400);
    }

/**
     * add WaterfallTag failed by descriptor url invalid on image url endWith
     * @param ApiTester $I
     */
    public function addAdTagWithDescriptorURLInvalidOnImageUrlEndWith(ApiTester $I)
    {
        $I->sendPOST(URL_API . '/adtags', [
            'adSlot' => PARAMS_AD_SLOT,
            'libraryAdTag' => [
                'html' => 'oki',
                'adNetwork' => PARAMS_AD_NETWORK,
                'adType' => 1,
                "descriptor" => [
                    "imageUrl" => "http://www.adTag-test.com/image.jpg_wrong", //this is invalid image URL
                    "targetUrl" => "http://www.adTag-test.com"
                ],
                'name' => 'adTag-test'
            ],
            'frequencyCap' => 300,
            'position' => 6,
            'active' => true,
            "rotation" => 50
        ]);
        //$I->seeResponseCodeIs(400);
        $I->seeResponseCodeIs(201); //now uncheck file extension
    }

    /**
     * edit WaterfallTag
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function editAdTag(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/adtags/' . $item['id'], [
            'adSlot' => PARAMS_AD_SLOT,
            'libraryAdTag' => [
                //'isReferenced' => true,
                'html' => 'oki',
                'visible' => false,
                'adNetwork' => PARAMS_AD_NETWORK,
                'adType' => 1,
                "descriptor" => [
                    "imageUrl" => "http://www.adTag-test.com/image.jpg",
                    "targetUrl" => "http://www.adTag-test.com"
                ],
                'name' => 'adTag-test'
            ],
            'frequencyCap' => 300,
            'position' => 6,
            'active' => true,
            "rotation" => 50
        ]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * edit WaterfallTag failed by null field
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function editAdTagWithNullField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/adtags/' . $item['id'], [
            'adSlot' => PARAMS_AD_SLOT,
            'libraryAdTag' => null, //this is null field
            'frequencyCap' => 300,
            'position' => 6,
            'active' => true,
            "rotation" => 50
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit WaterfallTag failed by unexpected field
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function editAdTagWithUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/adtags/' . $item['id'], [
            'adSlot' => PARAMS_AD_SLOT,
            'unexpected_field' => 'adTag-test', //this is unexpected field
            'libraryAdTag' => [
                //'isReferenced' => true,
                'html' => 'oki',
                'visible' => false,
                'adNetwork' => PARAMS_AD_NETWORK,
                'adType' => 1,
                "descriptor" => [
                    "imageUrl" => "http://www.adTag-test.com/image.jpg",
                    "targetUrl" => "http://www.adTag-test.com"
                ],
                'name' => 'adTag-test'
            ],
            'frequencyCap' => 300,
            'position' => 6,
            'active' => true,
            "rotation" => 50
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit WaterfallTag failed by wrong data
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function editAdTagWithWrongData(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/adtags/' . $item['id'], [
            'adSlot' => PARAMS_AD_SLOT,
            'libraryAdTag' => [
                //'isReferenced' => true,
                'html' => 'oki',
                'visible' => false,
                'adNetwork' => PARAMS_AD_NETWORK,
                'adType' => 1,
                "descriptor" => [
                    "imageUrl" => "http://www.adTag-test.com/image.jpg",
                    "targetUrl" => "http://www.adTag-test.com"
                ],
                'name' => 'adTag-test'
            ],
            'frequencyCap' => -300, //this is wrong data, must positive
            'position' => 6,
            'active' => true,
            "rotation" => 50
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit WaterfallTag failed by wrong data type
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function editAdTagWithWrongDataType(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPUT(URL_API . '/adtags/' . $item['id'], [
            'adSlot' => PARAMS_AD_SLOT,
            'libraryAdTag' => [
                //'isReferenced' => true,
                'html' => 'oki',
                'visible' => false,
                'adNetwork' => PARAMS_AD_NETWORK,
                'adType' => 1,
                "descriptor" => [
                    "imageUrl" => "http://www.adTag-test.com/image.jpg",
                    "targetUrl" => "http://www.adTag-test.com"
                ],
                'name' => 'adTag-test'
            ],
            'frequencyCap' => '300_wrong', //this is wrong data type, must number
            'position' => 6,
            'active' => true,
            "rotation" => 50
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch WaterfallTag
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function patchAdTag(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/adtags/' . $item['id'], [
            'adSlot' => PARAMS_AD_SLOT,
            'libraryAdTag' => [
                'html' => 'oki-edited',
            ],
            'frequencyCap' => 310
        ]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * patch WaterfallTag
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function patchAdTagMoveToLibrary(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/adtags/' . $item['id'], [
            'adSlot' => PARAMS_AD_SLOT,
            'libraryAdTag' => [
                'visible' => true,
                'name' => 'adTag-test-lib'
            ]
        ]);
        $I->seeResponseCodeIs(204);
    }

    /**
     * patch WaterfallTag failed by null field
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function patchAdTagNullField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/adtags/' . $item['id'], [
            'adSlot' => PARAMS_AD_SLOT,
            'libraryAdTag' => null, //this is field null
            'frequencyCap' => 300
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch WaterfallTag failed by unexpected field
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function patchAdTagWithUnexpectedField(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/adtags/' . $item['id'], [
            'adSlot' => PARAMS_AD_SLOT,
            'libraryAdTag' => [
                'html' => 'oki-edited',
            ],
            'unexpected_field' => 'oki', //this is unexpected field
            'frequencyCap' => 300
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch WaterfallTag failed by wrong data
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function patchAdTagWithWrongData(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/adtags/' . $item['id'], [
            'adSlot' => PARAMS_AD_SLOT,
            'libraryAdTag' => [
                'html' => 'oki-edited',
            ],
            'frequencyCap' => -300 //this is wrong data, must possitive
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * patch WaterfallTag failed by wrong data type
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function patchAdTagWithWrongDataType(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendPATCH(URL_API . '/adtags/' . $item['id'], [
            'adSlot' => PARAMS_AD_SLOT,
            'libraryAdTag' => [
                'html' => 'oki-edited',
            ],
            'frequencyCap' => '300_wrong' //this is wrong data type, must number
        ]);
        $I->seeResponseCodeIs(400);
    }

    /**
     * delete WaterfallTag
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function deleteAdTag(ApiTester $I)
    {
        $I->sendGet(URL_API . '/adtags');
        $item = array_pop($I->grabDataFromJsonResponse());

        $I->sendDELETE(URL_API . '/adtags/' . $item['id']);
        $I->seeResponseCodeIs(204);
    }

    /**
     * delete WaterfallTag failed cause by Not Existed
     * @depends addAdTag
     * @param ApiTester $I
     */
    public function deleteAdTagNotExisted(ApiTester $I)
    {
        $I->sendDELETE(URL_API . '/adtags/' . '-1');
        $I->seeResponseCodeIs(404);
    }

    /**
     * edit EstCpm of WaterfallTag
     * @param ApiTester $I
     */
    public function editEstCpmAdTag(ApiTester $I)
    {
        $I->sendPUT(URL_API . '/adtags/' . PARAMS_AD_TAG . '/estcpm' . '?endDate='. END_DATE .'&estCpm='. '12' .'&startDate=' . START_DATE);
        $I->seeResponseCodeIs(204);
    }

    /**
     * edit EstCpm of WaterfallTag failed by invalid
     * @param ApiTester $I
     */
    public function editEstCpmAdTagInvalid(ApiTester $I)
    {
        $I->sendPUT(URL_API . '/adtags/' . PARAMS_AD_TAG . '/estcpm' . '?endDate='. END_DATE .'&estCpm='. '-12' .'&startDate=' . START_DATE);
        $I->seeResponseCodeIs(400);
    }

    /**
     * edit EstCpm of WaterfallTag failed by wrong data type
     * @param ApiTester $I
     */
    public function editEstCpmAdTagWrongDataType(ApiTester $I)
    {
        $I->sendPUT(URL_API . '/adtags/' . PARAMS_AD_TAG . '/estcpm' . '?endDate='. END_DATE .'&estCpm='. '12_wrong' .'&startDate=' . START_DATE);
        $I->seeResponseCodeIs(400);
    }
}