<?php

date_default_timezone_set('Asia/Bangkok');

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();

$em = $container->get('doctrine.orm.entity_manager');
$adSlotManager = $container->get('tagcade.domain_manager.ad_slot');
$adNetworkManager = $container->get('tagcade.domain_manager.ad_network');
$userManager = $container->get('tagcade_user.domain_manager.user');

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

$eventCounter = new \Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter($adSlotManager->all());
$eventCounter->refreshTestData();

$reportCreator = new \Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportCreator($reportTypes, $eventCounter);
$reportCreator->setDate(new DateTime('1 days ago'));


$dailyReportCreator = new \Tagcade\Service\Report\PerformanceReport\Display\Creator\DailyReportCreator($em, $reportCreator);

$dailyReportCreator->createAndSave(
    $userManager->allPublisherRoles(),
    $adNetworkManager->all()
);