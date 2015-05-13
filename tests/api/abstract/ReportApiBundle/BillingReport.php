<?php

class BillingReport
{
    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I)
    {
    }

    /**
     * get Billing By Publisher
     * @param ApiTester $I
     */
    public function getBillingByPublisher(ApiTester $I)
    {
        $I->sendGET(URL_PERFORMANCE_REPORT . '/accounts/' . PARAMS_PUBLISHER . '?group=true&startDate=' . START_DATE . '&endDate=' . END_DATE);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get Billing By Publisher failed by not existed
     * @param ApiTester $I
     */
    public function getBillingByPublisherNotExisted(ApiTester $I)
    {
        $I->sendGET(URL_PERFORMANCE_REPORT . '/accounts/' . '-1' . '?group=true&startDate=' . START_DATE . '&endDate=' . END_DATE);
        $I->seeResponseCodeIs(404);
    }

    /**
     * get Billing By Publisher With StartDate Greater Than EndDate
     * @param ApiTester $I
     */
    public function getBillingByPublisherWithStartDateGreaterThanEndDate(ApiTester $I)
    {
        $I->sendGET(URL_PERFORMANCE_REPORT . '/accounts/' . PARAMS_PUBLISHER . '?group=true&startDate=' . END_DATE . '&endDate=' . START_DATE);
        $I->seeResponseCodeIs(400);
    }

    /**
     * get Billing By Site and Publisher
     * @param ApiTester $I
     */
    public function getBillingBySiteAndPublisher(ApiTester $I)
    {
        $I->sendGET(URL_PERFORMANCE_REPORT . '/accounts/' . PARAMS_PUBLISHER . '/sites?group=true&startDate=' . START_DATE . '&endDate=' . END_DATE);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get Billing By Site and Publisher failed by Publisher Not Existed
     * @param ApiTester $I
     */
    public function getBillingBySiteAndPublisherNotExisted(ApiTester $I)
    {
        $I->sendGET(URL_PERFORMANCE_REPORT . '/accounts/' . '-1' . '/sites?group=true&startDate=' . START_DATE . '&endDate=' . END_DATE);
        $I->seeResponseCodeIs(404);
    }

    /**
     * get Billing By Site and Publisher With StartDate Greater Than EndDate
     * @param ApiTester $I
     */
    public function getBillingBySiteAndPublisherWithStartDateGreaterThanEndDate(ApiTester $I)
    {
        $I->sendGET(URL_PERFORMANCE_REPORT . '/accounts/' . PARAMS_PUBLISHER . '/sites?group=true&startDate=' . END_DATE . '&endDate=' . START_DATE);
        $I->seeResponseCodeIs(400);
    }

    /**
     * get Billing By Site and Publisher
     * @param ApiTester $I
     */
    public function getBillingByPlatformAndDay(ApiTester $I)
    {
        $I->sendGET(URL_PERFORMANCE_REPORT . '/platform?group=true&platformBreakdown=day&startDate=' . START_DATE . '&endDate=' . END_DATE);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get Billing By Site and Publisher With StartDate Greater Than EndDate
     * @param ApiTester $I
     */
    public function getBillingByPlatformAndDayWithStartDateGreaterThanEndDate(ApiTester $I)
    {
        $I->sendGET(URL_PERFORMANCE_REPORT . '/platform?group=true&platformBreakdown=day&startDate=' . END_DATE . '&endDate=' . START_DATE);
        $I->seeResponseCodeIs(400);
    }

    /**
     * get Billing By Site and Account
     * @param ApiTester $I
     */
    public function getBillingByPlatformAndAccount(ApiTester $I)
    {
        $I->sendGET(URL_PERFORMANCE_REPORT . '/platform/accounts?group=true&platformBreakdown=account&startDate=' . START_DATE . '&endDate=' . END_DATE);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get Billing By Site and Account With StartDate Greater Than EndDate
     * @param ApiTester $I
     */
    public function getBillingByPlatformAndAccountWithStartDateGreaterThanEndDate(ApiTester $I)
    {
        $I->sendGET(URL_PERFORMANCE_REPORT . '/platform/accounts?group=true&platformBreakdown=account&startDate=' . END_DATE . '&endDate=' . START_DATE);
        $I->seeResponseCodeIs(400);
    }

    /**
     * get Billing By Site and Site
     * @param ApiTester $I
     */
    public function getBillingByPlatformAndSite(ApiTester $I)
    {
        $I->sendGET(URL_PERFORMANCE_REPORT . '/platform/sites?group=true&platformBreakdown=site&startDate=' . START_DATE . '&endDate=' . END_DATE);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get Billing By Site and Site With StartDate Greater Than EndDate
     * @param ApiTester $I
     */
    public function getBillingByPlatformAndSiteWithStartDateGreaterThanEndDate(ApiTester $I)
    {
        $I->sendGET(URL_PERFORMANCE_REPORT . '/platform/sites?group=true&platformBreakdown=site&startDate=' . END_DATE . '&endDate=' . START_DATE);
        $I->seeResponseCodeIs(400);
    }
}
