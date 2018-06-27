<?php


namespace Tagcade\Service\Optimization\KMeanClustering;


interface OptimumKMeanClusterServiceManagerInterface
{
    public function getOptimumCluster(array $points, $method);
    public function getRandomMethod();

}