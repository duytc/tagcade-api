<?php

use Phpml\Exception\InvalidArgumentException;
use Tagcade\Service\ArrayUtil;
use Tagcade\Service\Optimization\TagcadeKMeans;

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();

$points = [1.2, 1.3, 1.1, 3.1, 6.5, 6.6];
//$points = [[1, 1], [8, 7], [1, 2], [7, 8], [2, 1], [8, 9]];

$kMeanClusteringService =  $container->get('tagcade.service.optimization.kmean_clustering_service');

/*try {
    $kMeans = new TagcadeKMeans(2);
    $cluster = $kMeans->cluster($points);

} catch (InvalidArgumentException $e) {

}*/

try {
    $cluster = $kMeanClusteringService->getClusters($points);
} catch (InvalidArgumentException $e) {
    return $e;
}

$kMeanClusteringService =  $container->get('tagcade.service.optimization.kmean_clustering_service');

