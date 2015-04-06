<?php

/**
 * @group publisher
 */
class PerformanceReportPublisherCest extends PerformanceReport
{
    public function getAccountByPublisher(ApiTester $I) {
        $I->sendGet(URL_PERFORMANCE_REPORT.'/accounts/'.PARAMS_PUBLISHER.'?group=true&startDate='.START_DATE.'&endDate='.END_DATE);
    }

    public function getAdNetworkByPublisher(ApiTester $I) {
        $I->sendGet(URL_PERFORMANCE_REPORT.'/accounts/'.PARAMS_PUBLISHER.'/adnetworks?group=true&startDate='.START_DATE.'&endDate='.END_DATE);
    }

    public function getSiteByPublisher(ApiTester $I) {
        $I->sendGet(URL_PERFORMANCE_REPORT.'/accounts/'.PARAMS_PUBLISHER.'/sites?group=true&startDate='.START_DATE.'&endDate='.END_DATE);
    }

    public function getAdNetworkById(ApiTester $I) {
        $I->sendGet(URL_PERFORMANCE_REPORT.'/adnetworks/'.PARAMS_AD_NETWORK.'?group=true&startDate='.START_DATE.'&endDate='.END_DATE);
    }

    public function getAllSiteByAdNetwork(ApiTester $I) {
        $I->sendGet(URL_PERFORMANCE_REPORT.'/adnetworks/'.PARAMS_AD_NETWORK.'/sites?group=true&startDate='.START_DATE.'&endDate='.END_DATE);
    }

    public function getSiteByAdNetwork(ApiTester $I) {
        $I->sendGet(URL_PERFORMANCE_REPORT.'/adnetworks/'.PARAMS_AD_NETWORK.'/sites/'.PARAMS_SITE.'?group=true&startDate='.START_DATE.'&endDate='.END_DATE);
    }

    public function getAllAdTagByAdNetwork(ApiTester $I) {
        $I->sendGet(URL_PERFORMANCE_REPORT.'/adnetworks/'.PARAMS_AD_NETWORK.'/adtags?group=true&startDate='.START_DATE.'&endDate='.END_DATE);
    }

    public function getAllAdTagBySiteAndByAdNetwork(ApiTester $I) {
        $I->sendGet(URL_PERFORMANCE_REPORT.'/adnetworks/'.PARAMS_AD_NETWORK.'/sites/'.PARAMS_SITE.'/adtags?group=true&startDate='.START_DATE.'&endDate='.END_DATE);
    }

    public function getSiteById(ApiTester $I) {
        $I->sendGet(URL_PERFORMANCE_REPORT.'/sites/'.PARAMS_SITE.'?group=true&startDate='.START_DATE.'&endDate='.END_DATE);
    }

    public function getAdNetworkBySite(ApiTester $I) {
        $I->sendGet(URL_PERFORMANCE_REPORT.'/sites/'.PARAMS_SITE.'/adnetworks?group=true&startDate='.START_DATE.'&endDate='.END_DATE);
    }

    public function getAdSlotBySite(ApiTester $I) {
        $I->sendGet(URL_PERFORMANCE_REPORT.'/sites/'.PARAMS_SITE.'/adslots?group=true&startDate='.START_DATE.'&endDate='.END_DATE);
    }

    public function getAdTagBySite(ApiTester $I) {
        $I->sendGet(URL_PERFORMANCE_REPORT.'/sites/'.PARAMS_SITE.'/adtags?group=true&startDate='.START_DATE.'&endDate='.END_DATE);
    }

    public function getAdSlotById(ApiTester $I) {
        $I->sendGet(URL_PERFORMANCE_REPORT.'/adslots/'.PARAMS_AD_SLOT.'?group=true&startDate='.START_DATE.'&endDate='.END_DATE);
    }

    public function getAdTagByAdSlot(ApiTester $I) {
        $I->sendGet(URL_PERFORMANCE_REPORT.'/adslots/'.PARAMS_AD_SLOT.'/adtags?group=true&startDate='.START_DATE.'&endDate='.END_DATE);
    }

    public function getAdTagById(ApiTester $I) {
        $I->sendGet(URL_PERFORMANCE_REPORT.'/adtags/'.PARAMS_AD_TAG.'?group=true&startDate='.START_DATE.'&endDate='.END_DATE);
    }
}