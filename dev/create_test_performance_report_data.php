<?php

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', $debug = false);
$kernel->boot();

$container = $kernel->getContainer();

/** @var \Doctrine\ORM\EntityManagerInterface $em */
$em = $container->get('doctrine.orm.entity_manager');
$adSlotManager = $container->get('tagcade.domain_manager.ad_slot');
$ronAdSlotManager = $container->get('tagcade.domain_manager.ron_ad_slot');
$segmentRepository = $container->get('tagcade.repository.segment');
$adNetworkManager = $container->get('tagcade.domain_manager.ad_network');
$userManager = $container->get('tagcade_user.domain_manager.publisher');

$reportTypes = [
    $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.platform.ad_tag'),
    $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.platform.platform'),
    $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.platform.account'),
    $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.platform.site'),
    $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.platform.ad_slot'),

    $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.ad_network.ad_tag'),
    $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.ad_network.ad_network'),
    $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.ad_network.site'),

    $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.segment.segment'),
    $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.segment.ron_ad_slot'),
    $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.segment.ron_ad_tag')
];


$eventCounter = new \Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter($adSlotManager->allReportableAdSlots());
$reportCreator = new \Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportCreator($reportTypes, $eventCounter);
$dailyReportCreator = new \Tagcade\Service\Report\PerformanceReport\Display\Creator\DailyReportCreator($em, $reportCreator, $segmentRepository, $ronAdSlotManager);
$dailyReportCreator->setLogger($container->get('logger'));

$begin = new DateTime('2016-04-01');
$end = new DateTime('2016-04-05');

$today = new DateTime('today');
if ($end >= $today) {
    $end = new DateTime('yesterday');
}

// set true if need truncate all performance-partner reports in pass
$truncateAllHistoryPerformancePartnerReports = false; // false (default) or true

$minSlotOpportunities = 10000;
$maxSlotOpportunities = 100000;


$end = $end->modify('+1 day');
$interval = new DateInterval('P1D');
$dateRange = new DatePeriod($begin, $interval ,$end);

$em->getConnection()->getConfiguration()->setSQLLogger(null);

echo 'create performance report data...' . "\n";
$start = microtime(true);
foreach($dateRange as $date){
    echo sprintf("%s processing... @ %s\n", $date->format('Y-m-d'), date('c'));

    $eventCounter->refreshTestData($minSlotOpportunities, $maxSlotOpportunities);

    $dailyReportCreator
        ->setReportDate($date)
        ->createAndSave(
        $userManager->allActivePublishers(),
        $adNetworkManager->all()
    );

    echo sprintf("%s created @ %s\n", $date->format('Y-m-d'), date('c'));

    gc_collect_cycles();
}

// truncate all performance-partner reports in pass if is set above
if ($truncateAllHistoryPerformancePartnerReports) {
    echo '  > truncate all performance-partner reports in pass...' . "\n";
    $truncateSql = '
        set foreign_key_checks = 0;

        truncate table report_performance_display_hierarchy_partner_account;
        truncate table report_performance_display_hierarchy_partner_ad_network_ad_tag;
        truncate table report_performance_display_hierarchy_partner_ad_network_domain;
        truncate table report_performance_display_hierarchy_partner_ad_network_site_tag;
        TRUNCATE table report_performance_display_hierarchy_sub_publisher;
        TRUNCATE table report_performance_display_hierarchy_sub_publisher_ad_network;
        ';

    /** @var \Doctrine\DBAL\Driver\Statement $stmt */
    $stmt = $em->getConnection()->prepare($truncateSql);
    $stmt->execute();
    echo '  > truncate all performance-partner reports in pass... done!' . "\n";
} // end - truncate all performance-partner reports in pass
$totalTime = microtime(true) - $start;
echo sprintf('create performance report data... done after %d ms!' . "\n", $totalTime) ;
