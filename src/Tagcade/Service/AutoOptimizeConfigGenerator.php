<?php

namespace Tagcade\Service;

use Doctrine\Common\Collections\Collection;
use Tagcade\Domain\DTO\Core\AutoOptimizeCacheParam;
use Tagcade\Domain\DTO\Core\AutoOptimizeCacheSegmentParam;
use Tagcade\Domain\DTO\Core\AutoOptimizeCacheSlotParam;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Repository\Core\AdTagRepositoryInterface;

class AutoOptimizeConfigGenerator
{
    /** @var  AdTagRepositoryInterface */
    private $adTagRepository;

    /** @var AdSlotRepositoryInterface */
    private $adSlotRepository;

    /**
     * AutoOptimizeConfigGenerator constructor.
     * @param AdTagRepositoryInterface $adTagRepository
     * @param AdSlotRepositoryInterface $adSlotRepository
     */
    public function __construct(AdTagRepositoryInterface $adTagRepository, AdSlotRepositoryInterface $adSlotRepository)
    {
        $this->adTagRepository = $adTagRepository;
        $this->adSlotRepository = $adSlotRepository;
    }


    /**
     * input {
     *   "mappedBy": "adTagName",
     *   "adSlots": [1, 2, 3],
     *   "scores": [
     *     {
     *       "segmentFields": {
     *         "country": "US",
     *         "domain": "test.com.vn"
     *       },
     *       "adTagScores": [
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
     * @param AutoOptimizeCacheParam $param
     * @return array
     */
    public function generate(AutoOptimizeCacheParam $param)
    {
        $scores = $param->getScores();
        $adSlots = $param->getAdSlots();
        $slotAutoOptimizeConfigs = [];

        foreach ($adSlots as $adSlotId) {
            $configs = [];
            $scores = is_array($scores) ? $scores : [$scores];
            foreach ($scores as $score) {
                if (!$score instanceof AutoOptimizeCacheSegmentParam) {
                    continue;
                }

                $segmentFields = $score->getSegmentFields();

                // common segment key
                // segmentKey: domain, country, country.domain
                $segmentKey = (is_array($segmentFields) && !empty($segmentFields)) ? join('.', array_keys($segmentFields)) : '';
                if (empty($segmentKey)) {
                    $segmentKey = 'default'; // set key is "default", know as not has keys: domain, country or country.domain
                }

                if (!array_key_exists($segmentKey, $configs)) {
                    $configs[$segmentKey] = []; // init common segment
                }

                // special child segment key
                // segmentValue: US, abc.com, US.abc.com
                $segmentValue = join('.', array_values($segmentFields));
                if ($segmentValue === '.' || empty($segmentValue)) {
                    $segmentValue = 'default'; // set key is "default"
                }

                if (!array_key_exists($segmentValue, $configs[$segmentKey])) {
                    // avoid nested 'default' inside 'default'
                    if ($segmentKey !== 'default') {
                        $configs[$segmentKey][$segmentValue] = []; // init special child segments
                    }
                }

                $normalizedScores = $this->normalizeScores($adSlots, $score, $param->getMappedBy());

                if (array_key_exists($adSlotId, $normalizedScores)) {
                    if ($segmentKey === 'default') {
                        // avoid nested 'default' inside 'default'
                        // notice: if have multi scores with empty segments, the last will override previous and be used
                        $configs[$segmentKey] = $normalizedScores[$adSlotId];
                    } else {
                        $configs[$segmentKey][$segmentValue] = $normalizedScores[$adSlotId];
                    }
                }
            }

            $slotAutoOptimizeConfigs[$adSlotId] = $configs;
        }

        return $slotAutoOptimizeConfigs;
    }

    /**
     * input {
     * "adSlots": [1,2,3],
     * "adTagScores": [
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
     * @param array $adSlots
     * @param AutoOptimizeCacheSegmentParam $param
     * @param $identifier
     *
     * @return array
     */
    private function normalizeScores($adSlots, AutoOptimizeCacheSegmentParam $param, $identifier)
    {
        $adTagScores = $param->getAdTagScores();
        $result = [];
        foreach ($adSlots as $adSlotId) {
            $adSlot = $this->adSlotRepository->find($adSlotId);
            if (!$adSlot instanceof BaseAdSlotInterface) {
                continue;
            }

            $adTags = $adSlot->getAdTags();
            $adTags = $adTags instanceof Collection ? $adTags->toArray() : $adTags;
            $orderOptimizedAdTagIds = $this->addActiveAdTagsByScores($adTagScores, $identifier, $adSlotId);

            $missingAdTags = array_filter($adTags, function ($adTag) {
                return $adTag instanceof AdTagInterface && $adTag->isActive() && !$adTag->isPin();
            });
            $orderOptimizedAdTagIds = $this->addMissingAdTags($orderOptimizedAdTagIds, $missingAdTags);

            $pinAdTags = array_filter($adTags, function ($adTag) {
                return $adTag instanceof AdTagInterface && $adTag->isActive() && $adTag->isPin();
            });

            $orderOptimizedAdTagIds = $this->handleKeepAdTagsPositionWithPinned($orderOptimizedAdTagIds, $pinAdTags);

            $result[$adSlotId] = array_values($orderOptimizedAdTagIds);
        }

        return $result;
    }

    /**
     * @param $adTagScores
     * @param $identifier
     * @param $adSlotId
     * @return array
     */
    private function addActiveAdTagsByScores($adTagScores, $identifier, $adSlotId)
    {
        $adTagScores = array_filter($adTagScores, function ($adTagScore) {
            return is_array($adTagScore) && array_key_exists(AutoOptimizeCacheSlotParam::SCORE_KEY, $adTagScore);
        });

        usort($adTagScores, function ($a, $b) {
            $scoreA = floatval($a[AutoOptimizeCacheSlotParam::SCORE_KEY]);
            $scoreB = floatval($b[AutoOptimizeCacheSlotParam::SCORE_KEY]);
            if ($scoreA == $scoreB) {
                return 0;
            }

            return ($scoreA < $scoreB) ? 1 : -1;
        });

        // arrange adTag with the same position
        $orderAdTagIds = [];
        $oldScoreValue = 0;
        $keyAdTagOrder = 0;
        foreach ($adTagScores as $adTagScore) {
            if (!array_key_exists(AutoOptimizeCacheSlotParam::IDENTIFIER_KEY, $adTagScore)) {
                continue;
            }
            $identifierValue = $adTagScore[AutoOptimizeCacheSlotParam::IDENTIFIER_KEY];
            $scoreValue = $adTagScore[AutoOptimizeCacheSlotParam::SCORE_KEY];
            $adTag = null;
            switch ($identifier) {
                case AutoOptimizeCacheParam::IDENTIFY_BY_ID :
                    $adTag = $this->adTagRepository->findAdTagByIdAndSlotId($identifierValue, $adSlotId);
                    break;
                case AutoOptimizeCacheParam::IDENTIFY_BY_NAME :
                    $adTag = $this->adTagRepository->findAdTagByNameAndSlotId($identifierValue, $adSlotId);
                    break;
            }

            if ($adTag instanceof AdTagInterface && $adTag->isActive() && !$adTag->isPin()) {

                if (isset($oldScoreValue) && !empty($oldScoreValue)) {
                    // compare score value
                    $compareScore = $this->compareOptimizeScoreValue($oldScoreValue, $scoreValue);

                    if ($compareScore == true) {
                        if (!empty($orderAdTagIds)) {
                            $oldAdTagId = $orderAdTagIds[$keyAdTagOrder - 1];
                        }

                        if (isset($oldAdTagId) && is_array($oldAdTagId)) {
                            array_push($oldAdTagId, $adTag->getId());
                            $adTagIdsSameScore = $oldAdTagId;
                        } elseif (isset($oldAdTagId) && is_numeric($oldAdTagId)) {
                            $adTagIdsSameScore [] = $oldAdTagId;
                            $adTagIdsSameScore [] = $adTag->getId();
                        }
                    }
                }

                if (isset($adTagIdsSameScore) && !empty($adTagIdsSameScore)) {
                    unset($orderAdTagIds[$keyAdTagOrder - 1]);
                    $orderAdTagIds[$keyAdTagOrder - 1] = $adTagIdsSameScore;
                } else {
                    $orderAdTagIds[$keyAdTagOrder] = $adTag->getId();
                    $keyAdTagOrder++;
                }

                $oldScoreValue = $scoreValue;
                unset($adTagIdsSameScore);
            }
        }

        return $orderAdTagIds;
    }

    /**
     * @param $oldScoreValue
     * @param $newScoreValue
     * @return mixed
     */
    private function compareOptimizeScoreValue($oldScoreValue, $newScoreValue)
    {
        if ($oldScoreValue == $newScoreValue) {
            return true;
        }

        return false;
    }

    /**
     * @param $orderOptimizedAdTagIds
     * @param $adTagsNeedToBePinned
     * @return mixed
     */
    private function handleKeepAdTagsPositionWithPinned($orderOptimizedAdTagIds, $adTagsNeedToBePinned)
    {
        $adTagsNeedToBePinned = array_filter($adTagsNeedToBePinned, function ($adTag) {
            return $adTag instanceof AdTagInterface;
        });
        // handle keep origin position of adTag is pinned
        // e.g the optimized tags is [ 4, 3, 1, 2 ] and tag 2 is pinned with position is 3
        // then the expected optimized tags after handling pin is: [ 4, 3, 2, 1 ]

        //// sort $adTagsNeedToBePinned by position asc
        usort($adTagsNeedToBePinned, function ($adTag1, $adTag2) {
            /** @var AdTagInterface $adTag1 */
            /** @var AdTagInterface $adTag2 */
            if ($adTag1->getPosition() === $adTag2->getPosition()) {
                return 0;
            }

            return ($adTag1->getPosition() < $adTag2->getPosition()) ? -1 : 1;
        });

        //// do pin for needed pin ad tags
        foreach ($adTagsNeedToBePinned as $adTagNeedToBePinned) {
            if (!$adTagNeedToBePinned instanceof AdTagInterface) {
                continue;
            }
            $adTagPos = $adTagNeedToBePinned->getPosition();

            // append to end of $orderOptimizedAdTagIds if over length of $orderOptimizedAdTagIds
            // e.g $optimizedScore is [ 4, 2, 1 ] and ad tag 3 has position = 6, ad tag 5 has position = 8 (notice: 6 and 8 because we removed some paused ad tags before)
            // then expected $orderOptimizedAdTagIds is [ 4, 2, 1, 3, 5 ]
            if ($adTagPos > count($orderOptimizedAdTagIds)) {
                $orderOptimizedAdTagIds[] = $adTagNeedToBePinned->getId();
                continue;
            }

            // else, insert into middle...
            array_splice($orderOptimizedAdTagIds, $adTagPos - 1, 0, [$adTagNeedToBePinned->getId()]); // splice in at $adTagPos
        }

        return $orderOptimizedAdTagIds;
    }

    /**
     * @param $orderAdTagIds
     * @param $adTags
     * @return mixed
     */
    private function addMissingAdTags($orderAdTagIds, $adTags)
    {
        $adTags = array_filter($adTags, function ($adTag) {
            return $adTag instanceof AdTagInterface;
        });

        usort($adTags, function ($adTag1, $adTag2) {
            /** @var AdTagInterface $adTag1 */
            /** @var AdTagInterface $adTag2 */
            if ($adTag1->getPosition() === $adTag2->getPosition()) {
                return 0;
            }

            return ($adTag1->getPosition() < $adTag2->getPosition()) ? -1 : 1;
        });

        foreach ($adTags as $adTag) {
            if (!$adTag instanceof AdTagInterface) {
                continue;
            }

            $adTagId = $adTag->getId();

            // need to support adTag the same position
            $adTagIdExisted = false;
            foreach ($orderAdTagIds as $orderAdTagId) {

                if (!is_array($orderAdTagId) || empty($orderAdTagId)) {
                    continue;
                }

                // if $adTagId existed in $orderAdTagId -> continue: do not need to add in to  $orderAdTagIds
                if (in_array($adTagId, $orderAdTagId)) {
                    $adTagIdExisted = true;
                    break;
                }
            }

            // check adTAgIdExisted
            if ($adTagIdExisted == true) {
                continue;
            }

            // add missing adTags
            if (!in_array($adTagId, $orderAdTagIds)) {
                $orderAdTagIds [] = $adTagId;
            }
        }

        unset($adTagId, $adTag, $orderAdTagId);
        return $orderAdTagIds;
    }
}