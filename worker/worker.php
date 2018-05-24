<?php
// needed for handling signals
declare(ticks = 1);

const WORKER_EXIT_CODE_REQUEST_STOP_SUCCESS = 99;

$pid = getmypid();
$requestStop = false;

// You can test this by calling "kill -USR1 PID" where PID is the PID of this process, the process will end after the current job
pcntl_signal(SIGUSR1, function () use (&$requestStop, $pid, &$logger) {
    $logger->notice(sprintf("Worker PID %d has received a request to stop gracefully", $pid));
    $requestStop = true; // set reference value to true to stop worker loop after current job
});

// exit successfully after this time, supervisord will then restart
// this is to prevent any memory leaks from running PHP for a long time
const WORKER_TIME_LIMIT = 10800; // 3 hours
const TUBE_NAME = 'tagcade-api-worker';
const RESERVE_TIMEOUT = 10; // seconds
// Set the start time
$startTime = time();
$loader = require_once __DIR__ . '/../app/autoload.php';

require_once __DIR__ . '/../app/AppKernel.php';

$env = getenv('SYMFONY_ENV') ?: 'prod';
$debug = false;

if ($env == 'dev') {
    $debug = true;
}

$kernel = new AppKernel($env, $debug);
$kernel->boot();

/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
$container = $kernel->getContainer();

$logger = $container->get('logger');
$logHandler = new \Monolog\Handler\StreamHandler("php://stderr", \Monolog\Logger::DEBUG);
$logHandler->setFormatter(new \Monolog\Formatter\LineFormatter(null, null, false, true));
$logger->pushHandler($logHandler);

$entityManager = $container->get('doctrine.orm.entity_manager');
$queue = $container->get("leezy.pheanstalk");
// only tasks listed here are able to run
$worker = $container->get('tagcade.worker.workers.replicate_existing_lib_slot_tag_worker');
$availableWorkers = [
    $container->get('tagcade.worker.workers.update_revenue_worker'),
    $container->get('tagcade.worker.workers.update_cache_for_site_worker'),
    $container->get('tagcade.worker.workers.update_cache_for_channel_worker'),
    $container->get('tagcade.worker.workers.update_cache_for_publisher_worker'),
    $container->get('tagcade.worker.workers.update_ad_tag_status_for_ad_network_worker'),
    $container->get('tagcade.worker.workers.update_ad_tag_position_for_ad_network_and_sites_worker'),
    $container->get('tagcade.worker.workers.update_cache_for_video_waterfall_tag_worker'),
    $container->get('tagcade.worker.workers.remove_cache_for_video_waterfall_tag_worker'),
    $container->get('tagcade.worker.workers.update_video_demand_ad_tag_status_worker'),
    $container->get('tagcade.worker.workers.auto_pause_video_demand_ad_tag_worker'),
    $container->get('tagcade.worker.workers.auto_active_video_demand_ad_tag_worker'),
    $container->get('tagcade.worker.workers.deploy_video_demand_ad_tag_for_new_placement_rule_worker'),
    $container->get('tagcade.worker.workers.replicate_new_lib_slot_tag_worker'),
    $container->get('tagcade.worker.workers.replicate_existing_lib_slot_tag_worker'),
    $container->get('tagcade.worker.update_ad_tag_position_for_lib_slot_worker'),
    $container->get('tagcade.worker.workers.remove_cache_for_ad_slot_worker'),
    $container->get('tagcade.worker.workers.update_ad_slot_cache_due_to_display_blacklist_worker'),
    $container->get('tagcade.worker.workers.update_ad_slot_cache_worker'),
    $container->get('tagcade.worker.workers.update_ad_slot_cache_when_remove_optimization_integration_worker'),
    $container->get('tagcade.worker.workers.update_ad_slot_when_optimization_integration_change_worker'),
    $container->get('tagcade.worker.workers.update_video_waterfall_tag_when_optimization_integration_change_worker'),
    $container->get('tagcade.worker.workers.update_video_waterfall_tag_cache_when_remove_optimization_integration_worker'),
];

$workerPool = new \Tagcade\Worker\Pool($availableWorkers);
$logger->notice(sprintf("Worker PID %d has started", $pid));

while (true) {
    if ($requestStop) {
        // exit worker gracefully, supervisord will restart it
        $logger->notice(sprintf("Worker PID %d is stopping by user request", $pid));
        break;
    }

    if (time() > ($startTime + WORKER_TIME_LIMIT)) {
        // exit worker gracefully, supervisord will restart it
        $logger->notice(sprintf("Worker PID %d is stopping because time limit has been exceeded", $pid));
        break;
    }

    $job = $queue->watch(TUBE_NAME)
        ->ignore('default')
        ->reserve(RESERVE_TIMEOUT);

    if (!$job) {
        continue;
    }
    $worker = null; // important to reset the worker every loop
    $rawPayload = $job->getData();
    $payload = json_decode($rawPayload);
    if (!$payload) {
        $logger->error(sprintf('Received an invalid payload %s', $rawPayload));
        $queue->bury($job);
        continue;
    }
    $task = $payload->task;
    $params = $payload->params;
    $worker = $workerPool->findWorker($task);
    if (!$worker) {
        $logger->error(sprintf('The task "%s" is unknown', $task));
        $queue->bury($job);
        continue;
    }
    if (!$params instanceof Stdclass) {
        $logger->error(sprintf('The task parameters are not valid', $task));
        $queue->bury($job);
        continue;
    }
    $logger->notice(sprintf('Received job %s (ID: %s) with payload %s', $task, $job->getId(), $rawPayload));
    try {
        $worker->$task($params); // dynamic method call
        $logger->notice(sprintf('Job %s (ID: %s) with payload %s has been completed', $task, $job->getId(), $rawPayload));
        $queue->delete($job);
        // task finished successfully
    } catch (Exception $e) {
        $logger->warning(
            sprintf(
                'Job %s (ID: %s) with payload %s failed with an exception: %s',
                $task,
                $job->getId(),
                $rawPayload,
                $e->getMessage()
            )
        );
        $queue->bury($job);
    }
    $entityManager->clear();
    gc_collect_cycles();

    if (FALSE == $entityManager->getConnection()->ping()) {
        $entityManager->getConnection()->close();
        $entityManager->getConnection()->connect();
    }
}

if ($requestStop) {
    exit(WORKER_EXIT_CODE_REQUEST_STOP_SUCCESS); // otherwise use 0 status code
}