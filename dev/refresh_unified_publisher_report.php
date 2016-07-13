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
use Tagcade\Entity\Report\UnifiedReport\Publisher\PublisherReport;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\Publisher\PublisherReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\HistoryReportCreator;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\UnifiedReport\Selector\ReportBuilderInterface;

const PUBLISHER_ID = 2;
$START_DATE = new DateTime('2016-05-09');
$END_DATE = new DateTime('2016-06-15');

$today = new DateTime('today');
if ($END_DATE >= $today) {
    $END_DATE = new DateTime('yesterday');
}

$loader = require_once __DIR__ . '/../app/autoload.php';

require_once __DIR__ . '/../app/AppKernel.php';
$kernel = new AppKernel('dev', true);
$kernel->boot();

/** @var ContainerInterface $container */
$container = $kernel->getContainer();

/** @var EntityManagerInterface $em */
$em = $container->get('doctrine.orm.entity_manager');
$em->getConnection()->getConfiguration()->setSQLLogger(null);

/** @var PublisherManagerInterface $publisherManager */
$publisherManager = $container->get('tagcade_user.domain_manager.publisher');

echo 'Recalculating unified publisher report' . "\n";

/** @var ReportBuilderInterface $reportBuilder */
$reportBuilder = $container->get('tagcade.service.report.unified_report.selector.report_builder');
/** @var PublisherReportRepositoryInterface $publisherReportRepository */
$publisherReportRepository = $container->get('tagcade.repository.report.unified_report.publisher.publisher_report');
$publisher = $publisherManager->find(PUBLISHER_ID);

if (!$publisher instanceof PublisherInterface) {
    echo 'Publisher invalid' . "\n";
    return;
}

$END_DATE = $END_DATE->modify('+1 day');
$interval = new DateInterval('P1D');
$dateRange = new DatePeriod($START_DATE, $interval, $END_DATE);

$start = microtime(true);
foreach ($dateRange as $date) {
    echo sprintf("%s processing... @ %s\n", $date->format('Y-m-d'), date('c'));

    $aggregatedPublisherReport = $reportBuilder->getAllDemandPartnersByPartnerReport(
        $publisher,
        new Params($date, $date, true, true)
    );

    if (!$aggregatedPublisherReport) {
        continue;
    }

    $publisherReport = $reportBuilder->getAllDemandPartnersByDayReport(
        $publisher,
        new Params($date, $date, false, false)
    );

    if ($publisherReport) {
        $publisherReport = $publisherReport->getReports()[0];
    }

    if ($publisherReport instanceof PublisherReport) {
        echo sprintf('%s - %s :', $publisher->getUsername(), $date->format('Y-m-d')) . "\n";
        echo sprintf("\tImpressions: %d ---> %d", $publisherReport->getImpressions(), $aggregatedPublisherReport->getImpressions()) . "\n";
        echo sprintf("\tOpportunities: %d ---> %d", $publisherReport->getTotalOpportunities(), $aggregatedPublisherReport->getTotalOpportunities()) . "\n";
        echo sprintf("\tPassbacks: %d ---> %d", $publisherReport->getPassbacks(), $aggregatedPublisherReport->getPassbacks()) . "\n";
        echo sprintf("\tFill Rate: %f ---> %f", $publisherReport->getFillRate(), $aggregatedPublisherReport->getFillRate()) . "\n";
        echo sprintf("\tEstimated CPM: %f ---> %f", $publisherReport->getEstCpm(), $aggregatedPublisherReport->getEstCpm()) . "\n";
        echo sprintf("\tEstimated Revenue: %f ---> %f", $publisherReport->getEstRevenue(), $aggregatedPublisherReport->getEstRevenue()) . "\n";

        $report = new PublisherReport();
        $report->setName($publisher->getUsername())
            ->setTotalOpportunities($aggregatedPublisherReport->getTotalOpportunities())
            ->setImpressions($aggregatedPublisherReport->getImpressions())
            ->setPassbacks($aggregatedPublisherReport->getPassbacks())
            ->setFillRate()
            ->setPublisher($publisher)
            ->setDate($date)
            ->setEstRevenue($aggregatedPublisherReport->getEstRevenue())
            ->setEstCpm($aggregatedPublisherReport->getEstCpm())
        ;

        $publisherReportRepository->overrideSingleReport($report);
    }

    gc_collect_cycles();
}

echo 'All changes flushed to database' . "\n";
$totalTime = microtime(true) - $start;
echo sprintf('DONE after %d ms' . "\n", $totalTime) ;

