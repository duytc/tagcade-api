<?php


namespace Tagcade\Service\Optimization;



use Phpml\Clustering\KMeans;
use Tagcade\Service\Optimization\KMeanClustering\OptimumKMeanClusterServiceManagerInterface;

class KMeanClusteringService implements KMeanClusteringServiceInterface
{
    /**
     * @var OptimumKMeanClusterServiceManagerInterface
     */
    private $clusterServiceManager;


    /**
     * KMeanClusteringService constructor.
     * @param OptimumKMeanClusterServiceManagerInterface $clusterServiceManager
     */
    public function __construct(OptimumKMeanClusterServiceManagerInterface $clusterServiceManager)
    {
        $this->clusterServiceManager = $clusterServiceManager;
    }

    /**
     * @param array $points
     * @param null $numberClusters
     * @return array
     * @throws \Phpml\Exception\InvalidArgumentException
     */
    public function getClusters(array $points, $numberClusters = null)
    {
        $points = array_map(function ($item) {
            if (!is_array($item)) {
                return [$item];
            }
            return $item;
        }, $points);

        if (empty($numberClusters)) {
            $numberClusters = $this->clusterServiceManager->getOptimumCluster($points, $this->clusterServiceManager->getRandomMethod());
        }

        $kMeans = new TagcadeKMeans($numberClusters);

        return $kMeans->cluster($points);
    }
}