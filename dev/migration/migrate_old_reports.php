<?php

$loader = require_once __DIR__ . '/../../app/autoload.php';
require_once __DIR__ . '/../../app/AppKernel.php';

$kernel = new AppKernel('dev', $debug = false);
$kernel->boot();

$container = $kernel->getContainer();

$em = $container->get('doctrine.orm.entity_manager');
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
];

$dbh = new PDO('mysql:host=localhost;dbname=tagcade_temp', 'root', 'root');

require 'PdoEventCounter.php';

$eventCounter = new PdoEventCounter($dbh);

$reportCreator = new \Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportCreator($reportTypes, $eventCounter);

$dailyReportCreator = new \Tagcade\Service\Report\PerformanceReport\Display\Creator\DailyReportCreator($em, $reportCreator);

$begin = new DateTime('2015-01-01');
$end = new DateTime('2015-01-31');

$end = $end->modify('+1 day');
$interval = new DateInterval('P1D');
$dateRange = new DatePeriod($begin, $interval ,$end);

$em->getConnection()->getConfiguration()->setSQLLogger(null);

foreach($dateRange as $date){
    echo sprintf("%s processing... @ %s\n", $date->format('Y-m-d'), date('c'));

    $reportCreator->setDate($date);

    $dailyReportCreator->createAndSave(
        $userManager->allPublishers(),
        $adNetworkManager->all()
    );

    echo sprintf("%s created @ %s\n", $date->format('Y-m-d'), date('c'));

    gc_collect_cycles();
}