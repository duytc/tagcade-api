<?php

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', $debug = false);
$kernel->boot();

/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
$container = $kernel->getContainer();

$em = $container->get('doctrine.orm.entity_manager');
$adSlotManager = $container->get('tagcade.domain_manager.ad_slot');
$ronAdSlotManager = $container->get('tagcade.domain_manager.ron_ad_slot');
$segmentRepository = $container->get('tagcade.repository.segment');
/**
 * @var \Tagcade\DomainManager\AdNetworkManagerInterface $adNetworkManager
 */
$adNetworkManager = $container->get('tagcade.domain_manager.ad_network');
/**
 * @var \Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface $userManager
 */
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


$eventCounter = new \Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter($adSlotManager->all());
$reportCreator = new \Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportCreator($reportTypes, $eventCounter);
$dailyReportCreator = new \Tagcade\Service\Report\PerformanceReport\Display\Creator\DailyReportCreator($em, $reportCreator, $segmentRepository, $ronAdSlotManager);

$begin = new DateTime('2016-04-01');
$end = new DateTime('2016-04-20');

$today = new DateTime('today');
if ($end >= $today) {
    $end = new DateTime('yesterday');
}

$minSlotOpportunities = 10000;
$maxSlotOpportunities = 100000;

$publisherId = 2; // make sure to set this publisher to a test account

$publisher = $userManager->findPublisher($publisherId);
if (!$publisher instanceof \Tagcade\Model\User\Role\PublisherInterface) {
    throw new Exception(sprintf('Not found that publisher %d', $publisherId));
}

$publishers = [$publisher];

$adNetworks = [];
foreach ($publishers as $publisher) {
    $tmpAdNetworks = $adNetworkManager->getAdNetworksForPublisher($publisher);
    foreach ($tmpAdNetworks as $nw) {
        if (!in_array($nw, $adNetworks)) {
            $adNetworks[] = $nw;
        }
    }
}
$end = $end->modify('+1 day');
$interval = new DateInterval('P1D');
$dateRange = new DatePeriod($begin, $interval ,$end);

$em->getConnection()->getConfiguration()->setSQLLogger(null);

foreach($dateRange as $date){
    echo sprintf("%s processing... @ %s\n", $date->format('Y-m-d'), date('c'));

    $eventCounter->refreshTestData($minSlotOpportunities, $maxSlotOpportunities);

    $dailyReportCreator
        ->setReportDate($date)
        ->createReportsForPublishers(
            $publishers,
            $adNetworks
    );

    echo sprintf("%s created @ %s\n", $date->format('Y-m-d'), date('c'));

    gc_collect_cycles();
}
