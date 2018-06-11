<?php

namespace Tagcade\Service\Video;

use Doctrine\Common\Collections\Collection;
use Tagcade\Domain\DTO\Core\AutoOptimizeCacheSlotParam;
use Tagcade\Domain\DTO\Core\Video\AutoOptimizeVideoCacheParam;
use Tagcade\Domain\DTO\Core\Video\AutoOptimizeVideoCacheSegmentParam;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Repository\Core\VideoDemandAdTagRepositoryInterface;
use Tagcade\Repository\Core\VideoWaterfallTagRepositoryInterface;
use Tagcade\Service\ArrayUtil;

class AutoOptimizeVideoConfigGenerator
{
    /** @var  VideoDemandAdTagRepositoryInterface */
    private $videoDemandAdTagRepository;

    /** @var VideoWaterfallTagRepositoryInterface */
    private $videoWaterfallTagRepository;

    /**
     * AutoOptimizeConfigGenerator constructor.
     * @param VideoDemandAdTagRepositoryInterface $videoDemandAdTagRepository
     * @param VideoWaterfallTagRepositoryInterface $videoWaterfallTagRepository
     */
    public function __construct(VideoDemandAdTagRepositoryInterface $videoDemandAdTagRepository, VideoWaterfallTagRepositoryInterface $videoWaterfallTagRepository)
    {
        $this->videoDemandAdTagRepository = $videoDemandAdTagRepository;
        $this->videoWaterfallTagRepository = $videoWaterfallTagRepository;
    }


    /**
     * input {
     *   "mappedBy": "demandAdTagName",
     *   "waterfallTags": [1, 2, 3],
     *   "scores": [
     *     {
     *       "segmentFields": {
     *         "country": "US",
     *         "domain": "test.com.vn"
     *       },
     *       "demandAdTagScores": [
     *         {
     *           "identifier": "adTag1",
     *           "score": 0.1
     *         },
     *         {
     *           "identifier": "adTag3",
     *           "score": 0.2
     *         }
     *       ]
     *     }
     *   ]
     * }
     *
     * output {
     *   "3" : {
     *     "domain.country" : {
     *       "US.test.com.vn" : [3,1]
     *     }
     *   },
     *   "4" : {
     *     "domain.country" : {
     *       "US.test.com.vn" : [4,2]
     *     }
     *   }
     * }
     *
     * @param AutoOptimizeVideoCacheParam $param
     * @return array
     */
    public function generate(AutoOptimizeVideoCacheParam $param)
    {
        $scores = $param->getScores();
        $waterfallTags = $param->getWaterfallTags();
        $waterfallTagAutoOptimizeConfigs = [];

        foreach ($waterfallTags as $waterfallTagId) {
            $configs = [];
            $scores = is_array($scores) ? $scores : [$scores];
            foreach ($scores as $score) {
                if (!$score instanceof AutoOptimizeVideoCacheSegmentParam) {
                    continue;
                }

                $segmentFields = $score->getSegmentFields();
                $segmentKey = $this->buildSegmentKey($segmentFields);
                $segmentValue = $this->buildSegmentValue($segmentFields);

                if (!array_key_exists($segmentKey, $configs)) {
                    $configs[$segmentKey] = []; // init common segment
                }

                if (!array_key_exists($segmentValue, $configs[$segmentKey])) {
                    // avoid nested 'default' inside 'default'
                    if ($segmentKey !== 'default') {
                        $configs[$segmentKey][$segmentValue] = []; // init special child segments
                    }
                }

                $normalizedScores = $this->normalizeScores($waterfallTags, $score, $param->getMappedBy());

                if (!array_key_exists($waterfallTagId, $normalizedScores)) {
                    continue;
                }

                if ($segmentKey === 'default') {
                    // avoid nested 'default' inside 'default'
                    // notice: if have multi scores with empty segments, the last will override previous and be used
                    $configs[$segmentKey] = $normalizedScores[$waterfallTagId];
                } else {
                    $configs[$segmentKey][$segmentValue] = $normalizedScores[$waterfallTagId];
                }
            }

            $waterfallTagAutoOptimizeConfigs[$waterfallTagId] = $configs;
        }

        return $waterfallTagAutoOptimizeConfigs;
    }

    /**
     * @param $segmentFields
     * @return mixed
     */
    private function buildSegmentKey($segmentFields)
    {
        // common segment key
        // segmentKey: domain, country, country.domain
        $segmentKey = (is_array($segmentFields) && !empty($segmentFields)) ? join('.', array_keys($segmentFields)) : '';
        if (empty($segmentKey)) {
            $segmentKey = 'default'; // set key is "default", know as not has keys: domain, country or country.domain
        }

        return $segmentKey;
    }

    /**
     * @param $segmentFields
     * @return string
     */
    private function buildSegmentValue($segmentFields)
    {
        // special child segment key
        // segmentValue: US, abc.com, US.abc.com
        $segmentValue = join('.', array_values($segmentFields));
        if ($segmentValue === '.' || empty($segmentValue)) {
            $segmentValue = 'default'; // set key is "default"
        }

        return $segmentValue;
    }

    /**
     * input {
     * "waterfallTags": [1,2,3],
     * "demandAdTagScores": [
     *   {
     *     "identifier": "adTag1",
     *     "score": 0.1
     *   }, {
     *     "identifier": "adTag3",
     *     "score": 0.2
     *   }
     *  ]
     * }
     *
     * output {
     *   "1" : [4,3,5],
     *   "2": [3,5,1],
     *   "3": [6,7,8]
     * }
     *
     * @param array $waterfallTags
     * @param AutoOptimizeVideoCacheSegmentParam $param
     * @param $identifier
     *
     * @return array
     */
    private function normalizeScores($waterfallTags, AutoOptimizeVideoCacheSegmentParam $param, $identifier)
    {
        $demandAdTagScores = $param->getDemandTagScores();
        $result = [];
        foreach ($waterfallTags as $waterfallTagId) {
            $waterfallTag = $this->videoWaterfallTagRepository->find($waterfallTagId);
            if (!$waterfallTag instanceof VideoWaterfallTagInterface) {
                continue;
            }

            $videoDemandAdTags = $this->videoDemandAdTagRepository->getVideoDemandAdTagsForVideoWaterfallTag($waterfallTag);
            $videoDemandAdTags = $videoDemandAdTags instanceof Collection ? $videoDemandAdTags->toArray() : $videoDemandAdTags;

            $orderOptimizedAdTagIds = $this->addActiveDemandAdTagsByScores($demandAdTagScores, $identifier, $waterfallTagId);

            $missingDemandAdTags = array_filter($videoDemandAdTags, function ($demandAdTag) {
                return $demandAdTag instanceof VideoDemandAdTagInterface && $demandAdTag->getActive();
            });
            $orderOptimizedAdTagIds = $this->addMissingDemandAdTags($orderOptimizedAdTagIds, $missingDemandAdTags);

            $result[$waterfallTagId] = $orderOptimizedAdTagIds;
        }

        return $result;
    }

    /**
     * @param $demandAdTagScores
     * @param $identifier
     * @param $waterfallTagId
     * @return array
     */
    private function addActiveDemandAdTagsByScores($demandAdTagScores, $identifier, $waterfallTagId)
    {
        $demandAdTagScores = array_filter($demandAdTagScores, function ($demandAdTagScore) {
            return is_array($demandAdTagScore) && array_key_exists(AutoOptimizeCacheSlotParam::SCORE_KEY, $demandAdTagScore);
        });

        usort($demandAdTagScores, function ($a, $b) {
            $scoreA = floatval($a[AutoOptimizeCacheSlotParam::SCORE_KEY]);
            $scoreB = floatval($b[AutoOptimizeCacheSlotParam::SCORE_KEY]);
            if ($scoreA == $scoreB) {
                return 0;
            }

            return ($scoreA < $scoreB) ? 1 : -1;
        });

        $orderAdTagIds = [];
        foreach ($demandAdTagScores as $demandAdTagScore) {
            if (!array_key_exists(AutoOptimizeCacheSlotParam::IDENTIFIER_KEY, $demandAdTagScore)) {
                continue;
            }
            $identifierValue = $demandAdTagScore[AutoOptimizeCacheSlotParam::IDENTIFIER_KEY];
            $score = $demandAdTagScore[AutoOptimizeCacheSlotParam::SCORE_KEY];

            $demandTag = null;
            switch ($identifier) {
                case AutoOptimizeVideoCacheParam::IDENTIFY_BY_ID :
                    $demandTag = $this->videoDemandAdTagRepository->findDemandAdTagByIdAndWaterfallTagId($identifierValue, $waterfallTagId);
                    break;
                case AutoOptimizeVideoCacheParam::IDENTIFY_BY_NAME :
                    $demandTag = $this->videoDemandAdTagRepository->findDemandTagByNameAndWaterfallTagId($identifierValue, $waterfallTagId);
                    break;
            }

            if ($demandTag instanceof VideoDemandAdTagInterface && $demandTag->getActive()) {
                $orderAdTagIds[$demandTag->getId()] = $score;
            }
        }

        return $this->makeOrderAdTagsForRedis($orderAdTagIds);
    }

    /**
     * @param $orderAdTagIds
     * @return array
     */
    private function makeOrderAdTagsForRedis($orderAdTagIds)
    {
        $newOrderAdTagIds = [];
        //Handle case: two ad tags have same score
        foreach ($orderAdTagIds as $id => $score) {
            $newOrderAdTagIds[sprintf('%s', $score)][] = $id;
        }

        //If array has only one value, it is flatten
        foreach ($newOrderAdTagIds as $key => $values) {
            if (count($values) == 1) {
                $newOrderAdTagIds[$key] = reset($values);
            }
        }

        return array_values($newOrderAdTagIds);
    }

    /**
     * @param $orderDemandAdTagIds
     * @param $demandAdTags
     * @return mixed
     */
    private function addMissingDemandAdTags($orderDemandAdTagIds, $demandAdTags)
    {
        $arrayUtil = new ArrayUtil();
        $orderDemandAdTagIdsFlatten = $arrayUtil->array_flatten($orderDemandAdTagIds);

        $demandAdTags = array_filter($demandAdTags, function ($demandAdTag) {
            return $demandAdTag instanceof VideoDemandAdTagInterface;
        });

        usort($demandAdTags, function ($demandAdTag1, $demandAdTag2) {
            /** @var VideoDemandAdTagInterface $demandAdTag1 */
            /** @var VideoDemandAdTagInterface $demandAdTag2 */
            if ($demandAdTag1->getVideoWaterfallTagItem()->getPosition() === $demandAdTag2->getVideoWaterfallTagItem()->getPosition()) {
                return 0;
            }

            return ($demandAdTag1->getVideoWaterfallTagItem()->getPosition() < $demandAdTag2->getVideoWaterfallTagItem()->getPosition()) ? -1 : 1;
        });

        foreach ($demandAdTags as $demandAdTag) {
            if (!$demandAdTag instanceof VideoDemandAdTagInterface) {
                continue;
            }

            $demandAdTagId = $demandAdTag->getId();
            if (!in_array($demandAdTagId, $orderDemandAdTagIdsFlatten)) {
                $orderDemandAdTagIds[] = $demandAdTag->getId();
            }
        }

        return $orderDemandAdTagIds;
    }
}