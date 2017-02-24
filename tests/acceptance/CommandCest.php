<?php
use \AcceptanceTester;

class CommandCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests
    public function refreshCacheCommandTest(AcceptanceTester $I)
    {
        $tagCacheManager = $I->grabServiceFromContainer('tagcade.cache.display.tag_cache_manager');

        $tagCacheManager->refreshCache();

        // We just test if the command runs properly. Any exception occurs will cause this test fail otherwise it's successful
    }

    public function dailyRotateCommandTest(AcceptanceTester $I)
    {
        $userManager = $I->grabServiceFromContainer('tagcade_user.domain_manager.publisher');
        $adNetworkManager = $I->grabServiceFromContainer('tagcade.domain_manager.ad_network');
        $billingEditor = $I->grabServiceFromContainer('tagcade.service.report.performance_report.display.billing.billed_amount_editor');

        $dailyReportCreator = $I->grabServiceFromContainer('tagcade.service.report.performance_report.display.creator.daily_report_creator');

        // create report from redis data
        $reportDate = new DateTime('yesterday');
        $dailyReportCreator->setReportDate($reportDate);

        $dailyReportCreator->createAndSave(
            $userManager->allPublishers(),
            $adNetworkManager->all()
        );

        // recalculating billed amount
        $updatedCount = $billingEditor->updateBilledAmountThresholdForAllPublishers($reportDate);

        $I->assertGreaterThan(0, $updatedCount); // make sure there is data and creator can create that data
    }

    public function updateBilledAmountThresholdCommandTest(AcceptanceTester $I)
    {
        $billingEditor = $I->grabServiceFromContainer('tagcade.service.report.performance_report.display.billing.billed_amount_editor');
        $month = new \DateTime('yesterday');

        $updatedCount = $billingEditor->updateBilledAmountThresholdForAllPublishers($month);

        $I->assertGreaterThan(0, $updatedCount);
    }

    public function updateBillingHistoricalReportCommandTest(AcceptanceTester $I)
    {
        $publisherId = $I->getPublisherId();
        $cpmRate = 0.001;

        $endDate = new DateTime('yesterday');
        $startDate = new DateTime('20 days ago');

        $userManager = $I->grabServiceFromContainer('tagcade_user.domain_manager.publisher');
        $publisher = $userManager->findPublisher($publisherId);

        if (!$publisher instanceof \Tagcade\Model\User\Role\PublisherInterface) {
            throw new RuntimeException('that publisher is not existed');
        }

        $billingEditor = $I->grabServiceFromContainer('tagcade.service.report.performance_report.display.billing.billed_amount_editor');
        $updateResult = $billingEditor->updateHistoricalBilledAmount($publisher, (float)$cpmRate, $startDate, $endDate);

        $I->assertEquals(true, $updateResult);
    }

}