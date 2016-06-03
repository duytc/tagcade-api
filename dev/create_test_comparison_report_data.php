<?php

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', $debug = true);
$kernel->boot();

/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
$container = $kernel->getContainer();

/**
 * @var \Doctrine\ORM\EntityManagerInterface $em
 */
$em = $container->get('doctrine.orm.entity_manager');

/**
 * @var \Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface $userManager
 */
$userManager = $container->get('tagcade_user.domain_manager.publisher');
$reportComparisonCreator = $container->get('tagcade.service.report.unified_report.report_comparison_creator');


$begin = new DateTime('2016-04-01');
$end = new DateTime('2016-04-05');

$today = new DateTime('today');
if ($end >= $today) {
    $end = new DateTime('yesterday');
}

$override = false;
$publisherId = 2; // Id of publisher to generate data
$publisher = $userManager->findPublisher($publisherId);
// update comparison reports
try {
    $reportComparisonCreator->updateComparisonForPublisher($publisher, $begin, $end, $override);
} catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
    echo sprintf('Some data might have been created before. Use option "--override" instead');
}
