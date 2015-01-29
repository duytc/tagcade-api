<?php

$loader = require_once __DIR__ . '/../../app/autoload.php';
require_once __DIR__ . '/../../app/AppKernel.php';

$kernel = new AppKernel('dev', $debug = false);
$kernel->boot();

$container = $kernel->getContainer();

$em = $container->get('doctrine.orm.entity_manager');
$em->getConnection()->getConfiguration()->setSQLLogger(null);

const MIN_ID = 20784;
const MAX_ID = 25850;

$reportRepository = $em->getRepository('Tagcade\Entity\Report\SourceReport\Report');

$nextId = MIN_ID;

gc_enable();

while ($nextId <= MAX_ID) {
    $currentId = $nextId;

    echo sprintf("%d processing... @ %s\n", $currentId, date('c'));

    $nextId++;

    $report = $reportRepository->find($currentId);

    if (!$report) {
        continue;
    }

    $report->setCalculatedFields();

    $em->persist($report);
    $em->flush($report);

    $em->detach($report);
    $report = null;

    echo sprintf("%d finished @ %s\n", $currentId, date('c'));

    gc_collect_cycles();
}