<?php

use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\DomainManager\VideoDemandPartnerManager;
use Tagcade\Model\User\Role\PublisherInterface;

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', $debug = false);
$kernel->boot();

$container = $kernel->getContainer();

/** @var \Doctrine\ORM\EntityManagerInterface $em */
$em = $container->get('doctrine.orm.entity_manager');
/** @var PublisherManagerinterface $userManager */
$userManager = $container->get('tagcade_user.domain_manager.publisher');
/** @var VideoDemandPartnerManager $videoDemandPartnerManager */
$videoDemandPartnerManager = $container->get('tagcade.domain_manager.video_demand_partner');
$adSourceManager = $container->get('tagcade.domain_manager.video_demand_ad_tag');
$videoAdTagManager = $container->get('tagcade.domain_manager.video_waterfall_tag');
$logger = $container->get('logger');

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
$dailyReportCreator = new \Tagcade\Service\Report\VideoReport\Creator\DailyReportCreator($em, $reportCreator);
$dailyReportCreator->setLogger($container->get('logger'));

$publisherIds = [2,5];
$begin = new DateTime('2016-08-01');
$end = new DateTime('2016-09-07');


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

$publishers = [];
foreach ($publisherIds as $publisherId)
{
    $publisher = $userManager->findPublisher($publisherId);
    if (empty($publisher)) {
        $logger->info(sprintf('Not found publisher with id = %d in system', $publisherId));
        continue;
    }

    $publishers[] = $publisher;
}

if (empty($publishers)) {
    throw new Exception(sprintf('Not found any publisher for creating report'));
}

$videoDemandPartners=[];
/** @var PublisherInterface $publisher */
foreach ($publishers as $publisher) {
    $videoDemandPartner = $videoDemandPartnerManager->getVideoDemandPartnersForPublisher($publisher);
    if (empty($videoDemandPartner)) {
        $logger->info(sprintf('Not found Video demand partner for publisher with id = %d in system', $publisher->getId()));
        continue;
    }

    $videoDemandPartners = array_merge($videoDemandPartners, $videoDemandPartner);
}

if (empty($videoDemandPartners)) {
    throw new Exception(sprintf('Not found any video demand partner for creating report'));
}

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
         $publishers,
         $videoDemandPartners
    );

    echo sprintf("%s created @ %s\n", $date->format('Y-m-d'), date('c'));

    gc_collect_cycles();
}

$totalTime = microtime(true) - $start;
echo sprintf('create video report data... done after %d ms!' . "\n", $totalTime) ;
