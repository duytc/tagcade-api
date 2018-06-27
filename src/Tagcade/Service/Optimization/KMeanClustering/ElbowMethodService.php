<?php


namespace Tagcade\Service\Optimization\KMeanClustering;


use Exception;
use Phpml\Clustering\KMeans;
use Phpml\Math\Distance\Euclidean;
use Tagcade\Service\Optimization\TagcadeKMeans;

class ElbowMethodService implements OptimumKMeanClusterServiceInterface
{
    public function supportType($method)
    {
        return ($method == self::EL_BOW);
    }

    /**
     * @param array $points
     * @return false|int|string
     * @throws \Phpml\Exception\InvalidArgumentException
     */
    public function getOptimumCluster(array $points)
    {
        $maximumClusters = count($points) - 1;

        $sses = [];
        for ($i = 1; $i <= $maximumClusters; $i++) {
            $sses[$i] = $this->getTotalSSE($points, $i);
        }

        $minSses = min($sses);

        return array_search($minSses, $sses);
    }

    /**
     * @param array $points
     * @param $numCluster
     * @return mixed
     * @throws \Phpml\Exception\InvalidArgumentException
     * @throws Exception
     */
    private function getTotalSSE(array $points, $numCluster)
    {
        $kMeans = new TagcadeKMeans($numCluster);
        $clusters = $kMeans->cluster($points);

        if (!array_key_exists('totalSse', $clusters)) {
            throw new  Exception(sprintf('There is a error when running clustering, number cluster =%d', $numCluster));
        }

        return $clusters['totalSse'];
    }
}