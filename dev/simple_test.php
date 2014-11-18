<?php

date_default_timezone_set('Asia/Bangkok');

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();

$revenueEditor = $container->get('tagcade.service.revenue_editor');

$adTagManager = $container->get('tagcade.domain_manager.ad_tag');
$adTag = $adTagManager->find(2);

$revenueEditor->updateRevenueForAdTag($adTag, 100, new DateTime('yesterday'));

//$adNetworkManager = $container->get('tagcade.domain_manager.ad_network');

//$adNetwork = $adNetworkManager->find(1);

//$revenueEditor->updateRevenueForAdNetwork($adNetwork, 0.6, new DateTime('2014-10-30'));