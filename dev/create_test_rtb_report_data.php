<?php

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
$container = $kernel->getContainer();

writeln('### Start creating test data for all rtb ad slots ###');

writeln('    --> Preparing');
writeln('        ...');
/* config RtbDailyReportCreator */
$em = $container->get('doctrine.orm.entity_manager');
$userManager = $container->get('tagcade_user.domain_manager.publisher');

$creators = [
    $container->get('tagcade.service.report.rtb_report.creator.creators.hierarchy.platform_snapshot'),
    $container->get('tagcade.service.report.rtb_report.creator.creators.hierarchy.account_snapshot'),
    $container->get('tagcade.service.report.rtb_report.creator.creators.hierarchy.site_snapshot'),
    $container->get('tagcade.service.report.rtb_report.creator.creators.hierarchy.ad_slot_snapshot'),
    $container->get('tagcade.service.report.rtb_report.creator.creators.hierarchy.ron_ad_slot_snapshot'),
];

$adSlotManager = $container->get('tagcade.domain_manager.ad_slot');
$ronAdSlotManager = $container->get('tagcade.domain_manager.ron_ad_slot');
$reportableAdSlots = $adSlotManager->allReportableAdSlots();
$ronAdSlots = $ronAdSlotManager->all(); // TODO: call allReportableRonAdSlots() instead of
$rtbTestEventCounter = new \Tagcade\Service\Report\RtbReport\Counter\RtbTestEventCounter($reportableAdSlots, $ronAdSlots);

$rtbSnapshotReportCreator = new \Tagcade\Service\Report\RtbReport\Creator\RtbSnapshotReportCreator($creators, $rtbTestEventCounter);

$ronAdSlotManager = $container->get('tagcade.domain_manager.ron_ad_slot');

$rtbDailyReportCreator = new \Tagcade\Service\Report\RtbReport\Creator\RtbDailyReportCreator($em, $rtbSnapshotReportCreator, $ronAdSlotManager);

/* config date range */
$begin = new DateTime('2016-02-18');
$end = (new DateTime('2016-02-21'))->modify('+1 day');
$interval = new DateInterval('P1D');
$dateRange = new DatePeriod($begin, $interval, $end);

/* config sql logger disable */
$em->getConnection()->getConfiguration()->setSQLLogger(null);
writeln('    --> Preparing done!');

writeln('    --> Start saving data');
writeln('        ...');
foreach ($dateRange as $date) {
    echo sprintf("        ...%s processing... @ %s\n", $date->format('Y-m-d'), date('c'));

    $rtbTestEventCounter->setDate($date);
    $rtbTestEventCounter->refreshTestData();

    $rtbDailyReportCreator
        ->setReportDate($date)
        ->createAndSave($userManager->allActivePublishers());

    echo sprintf("        ...%s created @ %s\n", $date->format('Y-m-d'), date('c'));

    gc_collect_cycles();
}
writeln('    --> Finished saving data');

writeln('### Finished creating test data for all rtb ad slots ###');

function writeln($str)
{
    echo $str . PHP_EOL;
}
