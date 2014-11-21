<?php

$loader = require_once __DIR__.'/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformReportTypes;

$selectorService = $container->get('tagcade.service.statistics.selector');

$publishers = $container->get('tagcade_user.domain_manager.user')->allPublisherRoles();
$startDate = new DateTime('2014-11-01');
$endDate = new DateTime('2014-11-20');

$platformStatistics = $selectorService->getStatistics(new PlatformReportTypes\Platform($publishers), $startDate, $endDate);

var_dump($platformStatistics);
