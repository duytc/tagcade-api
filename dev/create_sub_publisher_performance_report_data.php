<?php

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', $debug = false);
$kernel->boot();

/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
$container = $kernel->getContainer();
$userManager = $container->get('tagcade_user.domain_manager.publisher');
$historicalReportCreator = $container->get('tagcade.service.report.performance_report.display.creator.history_report_creator');

$begin = new DateTime('2017-03-14');
$publisherId = 2; // make sure to set this publisher to a test account

$publisher = $userManager->findPublisher($publisherId);
if (!$publisher instanceof \Tagcade\Model\User\Role\PublisherInterface) {
    throw new Exception(sprintf('Not found that publisher %d', $publisherId));
}

$historicalReportCreator->setReportDate($begin);
$historicalReportCreator->createAndSaveForSinglePublisher($publisher);
