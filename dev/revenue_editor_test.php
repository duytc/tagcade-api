<?php

date_default_timezone_set('Asia/Bangkok');

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();

$em = $container->get('doctrine.orm.entity_manager');

//$adSlotManager = $container->get('tagcade.domain_manager.ad_slot');
//$adSlot = $adSlotManager->find(1);
//$adSlot->setName('My ad slot');
//$em->persist($adSlot);

//$revenueEditor = $container->get('tagcade.service.revenue_editor');
//
//$adTagManager = $container->get('tagcade.domain_manager.ad_tag');
//$adTag = $adTagManager->find(2);
//
//$revenueEditor->updateRevenueForAdTag($adTag, 100, new DateTime('yesterday'));

$dateUtil = $container->get('tagcade.service.date_util');
$remainingDays = $dateUtil->getNumberOfRemainingDatesOfMonth();

$numDaysPasswd = $dateUtil->getNumberOfDatesUpToToday();

$userManager = $container->get('tagcade_user.domain_manager.user');
$publisher = $userManager->findPublisher(2);
$billingEditor = $container->get('tagcade.service.report.performance_report.display.billing.billed_amount_editor');

$billingEditor->updateBilledAmountForPublisher($publisher, 1.5, new DateTime('20 days ago'), new DateTime('1 day ago'));

