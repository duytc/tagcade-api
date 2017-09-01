<?php

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', $debug = false);
$kernel->boot();

$container = $kernel->getContainer();

/** @var \Doctrine\ORM\EntityManagerInterface $em */
$em = $container->get('doctrine.orm.entity_manager');
$userManager = $container->get('tagcade_user.domain_manager.publisher');
$videoDemandPartnerManager = $container->get('tagcade.domain_manager.video_demand_partner');
$adSourceManager = $container->get('tagcade.domain_manager.video_demand_ad_tag');
$videoAdTagManager = $container->get('tagcade.domain_manager.video_waterfall_tag');

$reportCreators = [
    $container->get('tagcade.service.report.video_report.creator.creators.hierarchy.platform.demand_ad_tag'),
    $container->get('tagcade.service.report.video_report.creator.creators.hierarchy.platform.waterfall_tag'),
    $container->get('tagcade.service.report.video_report.creator.creators.hierarchy.platform.account'),
    $container->get('tagcade.service.report.video_report.creator.creators.hierarchy.platform.platform'),
    $container->get('tagcade.service.report.video_report.creator.creators.hierarchy.demand_partner.demand_ad_tag'),
    $container->get('tagcade.service.report.video_report.creator.creators.hierarchy.demand_partner.demand_partner'),
];

$redisCache = $container->get('tagcade.legacy.cache.performance_report_data');
$eventCounter = new \Tagcade\Service\Report\VideoReport\Counter\VideoTestEventCounter($videoAdTagManager->all(), $adSourceManager);
$reportCreator = new \Tagcade\Service\Report\VideoReport\Creator\ReportCreator($reportCreators, $eventCounter);
$dailyReportCreator = new \Tagcade\Service\Report\VideoReport\Creator\DailyReportCreator($em, $reportCreator, $userManager);
$dailyReportCreator->setLogger($container->get('logger'));

$begin = new DateTime('2016-10-01');
$end = new DateTime('2016-10-15');


$today = new DateTime('today');
if ($end >= $today) {
    $end = new DateTime('yesterday');
}

// set true if need truncate all video reports in pass
$truncateAllHistoryVideoReports = false; // false (default) or true

$minSlotOpportunities = 10000;
$maxSlotOpportunities = 100000;


$end = $end->modify('+1 day');
$interval = new DateInterval('P1D');
$dateRange = new DatePeriod($begin, $interval ,$end);

$em->getConnection()->getConfiguration()->setSQLLogger(null);
$minAdTagRequests = 10000;
$maxAdTagRequests = 100000;

echo 'create video report data...' . "\n";
$start = microtime(true);
foreach($dateRange as $date){
    echo sprintf("%s processing... @ %s\n", $date->format('Y-m-d'), date('c'));

    $eventCounter->refreshTestData($minAdTagRequests, $maxAdTagRequests, $date);

    $dailyReportCreator
        ->setReportDate($date)
        ->createAndSave(
        $userManager->allActivePublishers(),
        $videoDemandPartnerManager->all(),
        true
    );

    echo sprintf("%s created @ %s\n", $date->format('Y-m-d'), date('c'));

    gc_collect_cycles();
}

$totalTime = microtime(true) - $start;
echo sprintf('create video report data... done after %d ms!' . "\n", $totalTime) ;
