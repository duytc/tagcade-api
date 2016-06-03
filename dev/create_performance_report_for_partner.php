<?php

namespace tagcade\dev;

use AppKernel;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\HistoryReportCreator;

const PUBLISHER_ID = 2;
$START_DATE = new DateTime('2016-04-01');
$END_DATE = new DateTime('2016-04-05');

$today = new DateTime('today');
if ($END_DATE >= $today) {
    $END_DATE = new DateTime('yesterday');
}

$loader = require_once __DIR__ . '/../app/autoload.php';

require_once __DIR__ . '/../app/AppKernel.php';
$kernel = new AppKernel('dev', true);
$kernel->boot();
$override = true;
/** @var ContainerInterface $container */
$container = $kernel->getContainer();

/** @var EntityManagerInterface $em */
$em = $container->get('doctrine.orm.entity_manager');
$em->getConnection()->getConfiguration()->setSQLLogger(null);

/** @var PublisherManagerInterface $publisherManager */
$publisherManager = $container->get('tagcade_user.domain_manager.publisher');

echo 'Creating performance report for partner' . "\n";
/** @var HistoryReportCreator $historyReportCreator */
$historyReportCreator = $container->get('tagcade.service.report.performance_report.display.creator.history_report_creator');

$publisher = $publisherManager->find(PUBLISHER_ID);

if (!$publisher instanceof PublisherInterface) {
    echo 'Publisher invalid' . "\n";
    return;
}

$publishers[] = $publisher;

$END_DATE = $END_DATE->modify('+1 day');
$interval = new DateInterval('P1D');
$dateRange = new DatePeriod($START_DATE, $interval, $END_DATE);

$start = microtime(true);
foreach ($dateRange as $date) {
    echo sprintf("%s processing... @ %s\n", $date->format('Y-m-d'), date('c'));

    try {
        $historyReportCreator
            ->setReportDate($date)
            ->createAndSave($publishers, $override);

        echo sprintf("%s created       @ %s\n", $date->format('Y-m-d'), date('c'));

    } catch(UniqueConstraintViolationException $ex) {
        echo 'Some data might be created before. Try to use "--override" instead' . "\n";
        die;
    }

    gc_collect_cycles();
}

$totalTime = microtime(true) - $start;

echo sprintf('DONE after %d ms' . "\n", $totalTime) ;

