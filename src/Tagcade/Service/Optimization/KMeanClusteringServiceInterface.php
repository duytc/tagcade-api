<?php


namespace Tagcade\Service\Optimization;


interface KMeanClusteringServiceInterface
{
    public function getClusters(array $points, $numberClusters = null);
}