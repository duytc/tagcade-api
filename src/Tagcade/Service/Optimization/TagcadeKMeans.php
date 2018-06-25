<?php


namespace Tagcade\Service\Optimization;


use Phpml\Clustering\KMeans;
use Phpml\Clustering\KMeans\Space;
use Phpml\Math\Distance\Euclidean;

class TagcadeKMeans extends KMeans
{
    /**
     * @var int
     */
    private $clustersNumber;
    /**
     * @var int
     */
    private $initialization;

    /**
     * TagcadeKMeans constructor.
     * @param int $clustersNumber
     * @param int $initialization
     * @throws \Phpml\Exception\InvalidArgumentException
     */

    public function __construct(int $clustersNumber, int $initialization = self::INIT_KMEANS_PLUS_PLUS)
    {
        parent::__construct($clustersNumber, $initialization);

        $this->clustersNumber = $clustersNumber;
        $this->initialization = $initialization;
    }

    /**
     * @param array $samples
     * @return array
     * @throws \Phpml\Exception\InvalidArgumentException
     */
    public function cluster(array $samples)
    {
        $space = new Space(count(reset($samples)));
        foreach ($samples as $sample) {
            $space->addPoint($sample);
        }

        $clusters = [];
        $sse = 0.0;
        $measure = new Euclidean();
        foreach ($space->cluster($this->clustersNumber, $this->initialization) as $cluster) {
            $cluster = $cluster->toArray();

            $points = $cluster['points'];
            $centroid = $cluster['centroid'];

            foreach ($points as $point) {
                $sse += $measure->distance($centroid, $point) ** 2;
            }

            $clusters['clusters'][] = $points;
        }

        $clusters['totalSse'] = $sse;

        return $clusters;
    }
}