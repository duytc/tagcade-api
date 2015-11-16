<?php
namespace tagcade\dev;

use AppKernel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Site as SiteReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\AdSlot as AdSlotReportType;

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

/** @var ContainerInterface $container */
$container = $kernel->getContainer();

/**
 * @var \Tagcade\DomainManager\SiteManagerInterface $siteManager
 */
$siteManager = $container->get('tagcade.domain_manager.site');

/**
 * @var \Tagcade\DomainManager\AdSlotManagerInterface $adSlotManager
 */
$adSlotManager = $container->get('tagcade.domain_manager.ad_slot');

/**
 * @var \Tagcade\Service\Report\PerformanceReport\Display\Creator\SnapshotReportCreatorInterface $snapshotCreator
 */
$snapshotCreator = $container->get('tagcade.service.report.performance_report.display.creator.snapshot_report_creator');


$site = $siteManager->find(1);
$adSlot = $adSlotManager->find(23);
//$report = $snapshotCreator->getReport(new SiteReportType($site));
$report = $snapshotCreator->getReport(new AdSlotReportType($adSlot));

var_dump($report);