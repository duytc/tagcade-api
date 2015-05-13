<?php

class ActionLog
{
    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($I->getToken());
    }

    public function _after(ApiTester $I)
    {
    }

    /**
     * get ActionLogs
     * @param ApiTester $I
     */
    public function getActionLogs(ApiTester $I)
    {
        $I->sendGet(URL_ADMIN_API . '/logs?rowOffset=50&loginLogs=true&publisherId=' . PARAMS_PUBLISHER . '&statDate=' . START_DATE . '&endDate=' . END_DATE);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * get ActionLogs with Publisher not existed
     * @param ApiTester $I
     */
    public function getActionLogsWithPublisherNotExisted(ApiTester $I)
    {
        $I->sendGet(URL_ADMIN_API . '/logs?rowOffset=50&loginLogs=true&publisherId=' . '-1' . '&statDate=' . START_DATE . '&endDate=' . END_DATE);
        $I->seeResponseCodeIs(400);
    }

    /**
     * get ActionLogs with StartDate greater than EndDate
     * @param ApiTester $I
     */
    public function getActionLogsWithStartDateGreaterThanEndDate(ApiTester $I)
    {
        $I->sendGet(URL_ADMIN_API . '/logs?rowOffset=50&loginLogs=true&publisherId=' . PARAMS_PUBLISHER . '&statDate=' . END_DATE . '&endDate=' . START_DATE);
        $I->seeResponseCodeIs(400);
    }
}