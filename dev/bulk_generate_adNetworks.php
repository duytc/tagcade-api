<?php

use Tagcade\Service\ArrayUtil;

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();

$em = $container->get('doctrine.orm.entity_manager');

//$orderAdTagIds[$demandTag->getId()] = $score;

$orderAdTagIds = ['1' => 1, '2' => 0.8, '3' => 0.7, '4' => 0.7, '5' => 0.5];

//Case: There are number of ad tags have same scores
$newOrderAdTagIds = [];
foreach ($orderAdTagIds as $id => $score) {
    $newOrderAdTagIds[sprintf('%s', $score)][] = $id;
}

foreach ($newOrderAdTagIds as $key =>$values) {
    if(count($values) ==1 ) {
        $newOrderAdTagIds[$key] = reset($values);
    }
}

$newOrderAdTagIds = array_values($newOrderAdTagIds);

$arrayUtil = new ArrayUtil();
$orderDemandAdTagIdsFlatten = $arrayUtil->array_flatten($newOrderAdTagIds);

$newOrderAdTagIds = array_values($orderDemandAdTagIdsFlatten);