<?php


namespace Tagcade\Service\Optimization\KMeanClustering;


use Exception;

class OptimumKMeanClusterServiceManager implements OptimumKMeanClusterServiceManagerInterface
{
    static $allMethods = [
                          'elbow'=>OptimumKMeanClusterServiceInterface::EL_BOW
                          ];

    private $methods = [];

    /**
     * OptimumKMeanClusterServiceManager constructor.
     * @param array $optimumKMeanClusterServices
     */
    public function __construct(array $optimumKMeanClusterServices)
    {
        foreach ($optimumKMeanClusterServices as $optimumKMeanClusterService) {
            if(!$optimumKMeanClusterService instanceof OptimumKMeanClusterServiceInterface) {
                continue;
            }
            $this->methods[]= $optimumKMeanClusterService;
        }
    }

    /**
     * @param array $points
     * @param $method
     * @return mixed
     * @throws Exception
     */
    public function getOptimumCluster(array $points, $method)
    {
        $optimumKMeanClusterService =  $this->getOptimumKMeanClusterService($method);

        if (!$optimumKMeanClusterService  instanceof OptimumKMeanClusterServiceInterface) {
            throw new Exception(sprintf('Cannot find the proper method %s', $method));
        }

        return $optimumKMeanClusterService->getOptimumCluster($points);
    }

    /**
     * @param $methodName
     * @return mixed
     */
    private function getOptimumKMeanClusterService($methodName)
    {
        foreach ($this->methods as $method) {
            if(!$method instanceof OptimumKMeanClusterServiceInterface) {
                continue;
            }

            if($method->supportType($methodName)){
                return $method;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getRandomMethod()
    {
        return self::$allMethods[array_rand(self::$allMethods)];
    }
}