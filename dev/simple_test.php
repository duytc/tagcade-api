<?php

date_default_timezone_set('Asia/Bangkok');

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();

/**
 * @var \Tagcade\Service\Report\PerformanceReport\Display\RevenueCalculatorInterface
 */
$cpmCalculator = $container->get('tagcade.service.report.performance_report.revenue_calculator');

/**
 * @var \Doctrine\Common\Persistence\ObjectManager
 */
$entityManager = $container->get('doctrine.orm.entity_manager');
$repository = $entityManager->getRepository('Tagcade\Entity\Core\AdTag');

$adTag = $repository->find(2);

$revenue = $cpmCalculator->calculateRevenue($adTag, 10);

echo $revenue . "\n";