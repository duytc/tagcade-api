<?php

// exit successfully after this time, supervisord will then restart
// this is to prevent any memory leaks from running PHP for a long time
const WORKER_TIME_LIMIT = 3600; // 1 hour

// Set the start time
$startTime = time();

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', $debug = true);
$kernel->boot();

$container = $kernel->getContainer();
$queue = $container->get("leezy.pheanstalk");

// only tasks listed here are able to run
$availableTasks = [
    'updateRevenueForAdTag',
    'updateRevenueForAdNetwork',
    'updateRevenueForAdNetworkAndSite'
];

function stdErr($text) {
    file_put_contents('php://stderr', trim($text) . "\n", FILE_APPEND);
}

function stdOut($text) {
    file_put_contents('php://stdout', trim($text) . "\n", FILE_APPEND);
}

function updateRevenueForAdTag(StdClass $params) {
    global $container;

    $adTag = $container->get('tagcade.domain_manager.ad_tag')->find($params->adTagId);

    if (!$adTag) {
        throw new Exception('That ad tag does not exist');
    }

    $container->get('tagcade.service.revenue_editor')->updateRevenueForAdTag($adTag, $params->estCpm, $params->startDate, $params->endDate);
}

function updateRevenueForAdNetwork(StdClass $params) {
    global $container;

    $adNetwork = $container->get('tagcade.domain_manager.ad_network')->find($params->adNetworkId);

    if (!$adNetwork) {
        throw new Exception('That ad network does not exist');
    }

    $container->get('tagcade.service.revenue_editor')->updateRevenueForAdNetwork($adNetwork, $params->estCpm, $params->startDate, $params->endDate);
}

function updateRevenueForAdNetworkAndSite(StdClass $params) {
    global $container;

    $adNetwork = $container->get('tagcade.domain_manager.ad_network')->find($params->adNetworkId);

    if (!$adNetwork) {
        throw new Exception('That ad network does not exist');
    }

    $site = $container->get('tagcade.domain_manager.site')->find($params->siteId);

    if (!$site) {
        throw new Exception('That site does not exist');
    }

    $container->get('tagcade.service.revenue_editor')->updateRevenueForAdNetworkSite($adNetwork, $site, $params->estCpm, $params->startDate, $params->endDate);
}

while (true) {
    if (time() > ($startTime + WORKER_TIME_LIMIT)) {
        // exit worker gracefully, supervisord will restart it
        break;
    }

    $job = $queue->watch('tagcade-api-worker')
        ->ignore('default')
        ->reserve();

    $payload = unserialize($job->getData());

    if (!$payload) {
        stdErr(sprintf('Received an invalid payload'));
        $queue->bury($job);
        continue;
    }

    $task = $payload->task;
    $params = $payload->params;

    if (!is_string($task) || !in_array($task, $availableTasks, true) || !function_exists($task)) {
        stdErr(sprintf('The task "%s" is unknown', $task));
        $queue->bury($job);
        continue;
    }

    if (!$params instanceof Stdclass) {
        stdErr(sprintf('The task parameters are not valid', $task));
        $queue->bury($job);
        continue;
    }

    stdOut(sprintf('Received job %s', $job->getId()));

    try {
//        $task($params); // run the task function
        call_user_func($task, $params);
        stdOut(sprintf('Job %s has been completed', $job->getId()));
        $queue->delete($job);
        // task finished successfully
    } catch (Exception $e) {
        stdOut(sprintf('Job %s failed with an exception: %s', $job->getId(), $e->getMessage()));
        $queue->bury($job);
    }
}