<?php


namespace Tagcade\Service\Optimization\KMeanClustering;


interface OptimumKMeanClusterServiceInterface
{
    const EL_BOW = 'elbow';

    public function supportType($method);

    public function getOptimumCluster(array $points);
}