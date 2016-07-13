<?php

namespace tagcade\dev;

use AppKernel;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Entity\Report\UnifiedReport\Publisher\PublisherReport;
use Tagcade\Entity\Report\UnifiedReport\Publisher\SubPublisherReport;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\Publisher\SubPublisherReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Publisher\PublisherReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\UnifiedReport\Selector\ReportBuilderInterface;

const PUBLISHER_ID = 2;
$START_DATE = new DateTime('2016-06-09');
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

/** @var SubPublisherReportRepositoryInterface $subPublisherReportRepository */
$subPublisherReportRepository = $container->get('tagcade.repository.report.unified_report.publisher.sub_publisher_report');

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

    refreshPublisherReport($publisher, $date);

    $subPublishers = $publisher->getSubPublishers();

    /** @var PublisherInterface $subPublisher */
    foreach($subPublishers as $subPublisher) {
        refreshSubPublisherReport($subPublisher, $date);
    }

    gc_collect_cycles();
}

echo 'All changes flushed to database' . "\n";
$totalTime = microtime(true) - $start;
echo sprintf('DONE after %d ms' . "\n", $totalTime) ;

function refreshPublisherReport(PublisherInterface $publisher, \DateTime $date) {

    global $reportBuilder, $publisherReportRepository;
    $aggregatedPublisherReport = $reportBuilder->getAllDemandPartnersByPartnerReport(
        $publisher,
        new Params($date, $date, true, true)
    );

    if (!$aggregatedPublisherReport) {
        return;
    }

    $publisherReport = $reportBuilder->getAllDemandPartnersByDayReport(
        $publisher,
        new Params($date, $date, false, false)
    );

    if ($publisherReport) {
        $publisherReport = $publisherReport->getReports()[0];
    }

    if ($publisherReport instanceof PublisherReport) {
        echo sprintf('Publisher - %s :', $publisher->getUsername()) . "\n";
        echo sprintf("\tImpressions: %d ---> %d", $publisherReport->getImpressions(), $aggregatedPublisherReport->getImpressions()) . "\n";
        echo sprintf("\tOpportunities: %d ---> %d", $publisherReport->getTotalOpportunities(), $aggregatedPublisherReport->getTotalOpportunities()) . "\n";
        echo sprintf("\tPassbacks: %d ---> %d", $publisherReport->getPassbacks(), $aggregatedPublisherReport->getPassbacks()) . "\n";
        echo sprintf("\tFill Rate: %g ---> %g", $publisherReport->getFillRate(), $aggregatedPublisherReport->getFillRate()) . "\n";
        echo sprintf("\tEstimated CPM: %g ---> %g", $publisherReport->getEstCpm(), $aggregatedPublisherReport->getEstCpm()) . "\n";
        echo sprintf("\tEstimated Revenue: %g ---> %g", $publisherReport->getEstRevenue(), $aggregatedPublisherReport->getEstRevenue()) . "\n";

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
}

function refreshSubPublisherReport(PublisherInterface $subPublisher, $date) {
    global $reportBuilder, $subPublisherReportRepository;
    $aggregatedSubPublisherReport = $reportBuilder->getAllDemandPartnersByPartnerReport(
        $subPublisher,
        new Params($date, $date, true, true)
    );

    if (!$aggregatedSubPublisherReport) {
        return;
    }

    $subPublisherReport = $reportBuilder->getAllDemandPartnersByDayReport(
        $subPublisher,
        new Params($date, $date, false, false)
    );

    if ($subPublisherReport) {
        $subPublisherReport = $subPublisherReport->getReports()[0];
    }

    if ($subPublisherReport instanceof SubPublisherReport) {
        echo sprintf('SubPublisher - %s :', $subPublisher->getUsername()) . "\n";
        echo sprintf("\tImpressions: %d ---> %d", $subPublisherReport->getImpressions(), $aggregatedSubPublisherReport->getImpressions()) . "\n";
        echo sprintf("\tOpportunities: %d ---> %d", $subPublisherReport->getTotalOpportunities(), $aggregatedSubPublisherReport->getTotalOpportunities()) . "\n";
        echo sprintf("\tPassbacks: %d ---> %d", $subPublisherReport->getPassbacks(), $aggregatedSubPublisherReport->getPassbacks()) . "\n";
        echo sprintf("\tFill Rate: %g ---> %g", $subPublisherReport->getFillRate(), $aggregatedSubPublisherReport->getFillRate()) . "\n";
        echo sprintf("\tEstimated CPM: %g ---> %g", $subPublisherReport->getEstCpm(), $aggregatedSubPublisherReport->getEstCpm()) . "\n";
        echo sprintf("\tEstimated Revenue: %g ---> %g", $subPublisherReport->getEstRevenue(), $aggregatedSubPublisherReport->getEstRevenue()) . "\n";

        $report = new SubPublisherReport();
        $report->setName($subPublisher->getUsername())
            ->setTotalOpportunities($aggregatedSubPublisherReport->getTotalOpportunities())
            ->setImpressions($aggregatedSubPublisherReport->getImpressions())
            ->setPassbacks($aggregatedSubPublisherReport->getPassbacks())
            ->setFillRate()
            ->setSubPublisher($subPublisher)
            ->setDate($date)
            ->setEstRevenue($aggregatedSubPublisherReport->getEstRevenue())
            ->setEstCpm($aggregatedSubPublisherReport->getEstCpm())
        ;

        $subPublisherReportRepository->overrideSingleReport($report);
    }
}



