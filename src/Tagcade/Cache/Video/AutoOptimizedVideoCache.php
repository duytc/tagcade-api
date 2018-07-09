<?php

namespace Tagcade\Cache\Video;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Redis;
use Tagcade\Cache\Video\Refresher\VideoWaterfallTagCacheRefresherInterface;
use Tagcade\Domain\DTO\Core\Video\AutoOptimizeVideoCacheParam;
use Tagcade\DomainManager\VideoDemandAdTagManagerInterface;
use Tagcade\DomainManager\VideoWaterfallTagManagerInterface;
use Tagcade\Entity\Core\VideoWaterfallTagItem;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Service\Video\AutoOptimizeVideoConfigGenerator;

class AutoOptimizedVideoCache implements AutoOptimizedVideoCacheInterface
{
    const REDIS_CONNECT_TIMEOUT_IN_SECONDS = 2;
    const NAMESPACE_CACHE_KEY = 'video:waterfall_tag:%s:tag_config';
    const CACHE_KEY_WATERFALL_TAG = 'all_demand_ad_tags_array';
    const NAMESPACE_CACHE_KEY_VERSION = 'TagcadeNamespaceCacheKey[%s]';
    /**
     * @var Redis
     */
    static private $redis;

    private $host;

    private $port;

    /** @var VideoDemandAdTagManagerInterface */
    private $demandAdTagManager;

    /** @var VideoWaterfallTagManagerInterface */
    private $waterfallTagManager;

    /** @var  AutoOptimizeVideoConfigGenerator */
    private $autoOptimizeVideoConfigGenerator;

    /** @var VideoWaterfallTagCacheRefresherInterface */
    private $videoWaterfallTagCacheRefresher;

    /**
     * AutoOptimizedCache constructor.
     * @param $host
     * @param $port
     * @param VideoDemandAdTagManagerInterface $demandAdTagManager
     * @param VideoWaterfallTagManagerInterface $waterfallTagManager
     * @param AutoOptimizeVideoConfigGenerator $autoOptimizeVideoConfigGenerator
     * @param VideoWaterfallTagCacheRefresherInterface $videoWaterfallTagCacheRefresher
     */
    public function __construct($host, $port, VideoDemandAdTagManagerInterface $demandAdTagManager, VideoWaterfallTagManagerInterface $waterfallTagManager,
                                AutoOptimizeVideoConfigGenerator $autoOptimizeVideoConfigGenerator, VideoWaterfallTagCacheRefresherInterface $videoWaterfallTagCacheRefresher)
    {
        $this->host = $host;
        $this->port = $port;

        $this->getRedis();

        $this->demandAdTagManager = $demandAdTagManager;
        $this->waterfallTagManager = $waterfallTagManager;
        $this->videoWaterfallTagCacheRefresher = $videoWaterfallTagCacheRefresher;
        $this->autoOptimizeVideoConfigGenerator = $autoOptimizeVideoConfigGenerator;
    }

    /**
     * @return Redis
     */
    private function getRedis()
    {
        if (!self::$redis instanceof Redis) {
            self::$redis = new Redis();

            self::$redis->connect($this->host, $this->port, self::REDIS_CONNECT_TIMEOUT_IN_SECONDS);
            self::$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
        }

        return self::$redis;
    }

    /**
     * @param AutoOptimizeVideoCacheParam $param
     * @return mixed|void
     */
    public function updateCacheForWaterfallTags(AutoOptimizeVideoCacheParam $param)
    {
        $waterfallTagAutoOptimizeConfigs = $this->autoOptimizeVideoConfigGenerator->generate($param);
        $waterfallTags = $param->getWaterfallTags();

        foreach ($waterfallTags as $waterfallTag) {
            $videoWaterFallTag = $this->waterfallTagManager->find($waterfallTag);

            if (!$videoWaterFallTag instanceof VideoWaterfallTagInterface) {
                continue;
            }

            if (!$videoWaterFallTag->isAutoOptimize()) {
                $this->updateAutoOptimizedVideoConfigForWaterfallTag($waterfallTag, []);
                continue;
            }

            if (array_key_exists($waterfallTag, $waterfallTagAutoOptimizeConfigs)) {
                $this->updateAutoOptimizedVideoConfigForWaterfallTag($waterfallTag, $waterfallTagAutoOptimizeConfigs[$waterfallTag]);
            }
        }
    }

    /**
     * @param $waterfallTagId
     * @param array $autoOptimizedConfig
     * @return bool|mixed
     */
    public function updateAutoOptimizedVideoConfigForWaterfallTag($waterfallTagId, array $autoOptimizedConfig)
    {
        $waterfallTag = $this->waterfallTagManager->find($waterfallTagId);

        if (!$waterfallTag instanceof VideoWaterfallTagInterface) {
            return false;
        }

        $this->videoWaterfallTagCacheRefresher->refreshVideoWaterfallTag($waterfallTag, array('autoOptimize' => $autoOptimizedConfig));

        return true;
    }

    /**
     * @param AutoOptimizeVideoCacheParam $param
     * @return mixed
     */
    public function getPreviewPositionForWaterfallTags(AutoOptimizeVideoCacheParam $param)
    {
        $waterfallTagAutoOptimizeConfigs = $this->autoOptimizeVideoConfigGenerator->generate($param);
        $waterfallTags = $param->getWaterfallTags();
        $previewPositionResult = [];

        foreach ($waterfallTags as $waterfallTag) {
            $waterfallTag = $this->waterfallTagManager->find($waterfallTag);
            if (!$waterfallTag instanceof VideoWaterfallTagInterface) {
                continue;
            }

            // get current version cache
            $currentPosition = $this->getCurrentPositionDemandAdTags($waterfallTag, $param->getMappedBy());
            $optimizePosition = $this->getOptimizePositionDemandAdTags($waterfallTagAutoOptimizeConfigs[$waterfallTag->getId()], $param->getMappedBy());

            $key = sprintf("%s (ID: %s)", $waterfallTag->getId(), $waterfallTag->getId());
            $previewPositionResult[$key]['current'] = $currentPosition;
            $previewPositionResult[$key]['optimize'] = $optimizePosition;
        }

        return $previewPositionResult;
    }

    /**
     * @param VideoWaterfallTagInterface $waterfallTag
     * @param $mappedBy
     * @return array
     */
    protected function getCurrentPositionDemandAdTags(VideoWaterfallTagInterface $waterfallTag, $mappedBy)
    {
        // get preview demandAdTag position
        $currentDemandAdTags = [];
        $currentWaterfallTagCache = $this->getWaterfallTagCache($waterfallTag->getUuid());

        if (isset($currentWaterfallTagCache['autoOptimize']['default']) && !empty($currentWaterfallTagCache['autoOptimize']['default'])) {
            $currentDemandAdTags = $currentWaterfallTagCache['autoOptimize']['default'];
        } else {
            if (isset($currentWaterfallTagCache['waterfall']) && !empty($currentWaterfallTagCache['waterfall'])) {
                // get demand tags from waterfall
                $demandAdTags = [];

                $waterfallTagItems = $currentWaterfallTagCache['waterfall'];
                if (!is_array($waterfallTagItems)) {
                    return [];
                }

                foreach ($waterfallTagItems as $waterfallTagItem) {
                    if (!is_array($waterfallTagItem)
                        || !array_key_exists('demandTags', $waterfallTagItem)
                        || !is_array($waterfallTagItem['demandTags'])
                    ) {
                        continue;
                    }

                    $demandAdTags[] = $waterfallTagItem['demandTags'];
                }

                // map demand tags
                foreach ($demandAdTags as $demandAdTagsInAPosition) {
                    if (!is_array($demandAdTagsInAPosition)) {
                        continue;
                    }

                    $currentAdTagsItem = [];
                    foreach ($demandAdTagsInAPosition as $demandAdTagItem) {
                        $currentAdTagsItem[] = $demandAdTagItem['id'];
                    }

                    if (!empty($currentAdTagsItem)) {
                        $currentDemandAdTags[] = $currentAdTagsItem;
                    }
                }
            } else {
                $demandAdTags = $this->demandAdTagManager->getVideoDemandAdTagsForVideoWaterfallTag($waterfallTag);
                $demandAdTags = $demandAdTags instanceof Collection ? $demandAdTags->toArray() : $demandAdTags;
                foreach ($demandAdTags as $demandAdTag) {
                    if (!$demandAdTag instanceof VideoDemandAdTagInterface) {
                        continue;
                    }

                    $currentDemandAdTags[$demandAdTag->getVideoWaterfallTagItem()->getPosition()][] = $demandAdTag->getId();
                }

                // because the keys of $currentDemandAdTags (that are waterfall tag item positions) may not sorted asc
                // so we must sort key now
                ksort($currentDemandAdTags, SORT_ASC);
            }
        }

        return $this->normalizeDemandAdTags($currentDemandAdTags, $mappedBy);
    }

    /**
     * @param $waterfallTagId
     * @return bool|string
     */
    protected function getWaterfallTagCache($waterfallTagId)
    {
        $this->getRedis();

        $nameSpaceCacheKey = sprintf(self::NAMESPACE_CACHE_KEY, $waterfallTagId);
        $keyGetCurrentVersion = sprintf(self::NAMESPACE_CACHE_KEY_VERSION, $nameSpaceCacheKey);
        $currentVersionForWaterfallTag = (int)self::$redis->get($keyGetCurrentVersion);

        $cacheKey = sprintf('%s[%s][%s]', $nameSpaceCacheKey, self::CACHE_KEY_WATERFALL_TAG, $currentVersionForWaterfallTag);
        return self::$redis->get($cacheKey);
    }

    /**
     * @param array $currentDemandAdTags
     * @param $mappedBy
     * @return array
     */
    private function normalizeDemandAdTags(array $currentDemandAdTags, $mappedBy)
    {
        $currentDemandAdTags = array_map(function ($item) {
            if (!empty($item)) {
                return is_array($item) ? $item : [$item];
            }
        }, $currentDemandAdTags);

        if ($mappedBy == 'demandAdTagName') {
            $currentDemandAdTags = array_map(function ($demandAdTagId) {
                if (!is_array($demandAdTagId)) {   /// support for old cache : when groupt have 1 demandAdtag
                    $demandAdTag = $this->demandAdTagManager->find($demandAdTagId);
                    if ($demandAdTag instanceof VideoDemandAdTagInterface && $demandAdTag->getActive()) {
                        return $demandAdTag->getName();
                    }
                } else {
                    return array_map(function ($tag) {
                        if (!is_array($tag)) { // support for group have 1 demandAdtag
                            $demandAdTag = $this->demandAdTagManager->find($tag);
                            if ($demandAdTag instanceof VideoDemandAdTagInterface && $demandAdTag->getActive()) {
                                return $demandAdTag->getName();
                            }
                        } else {
                            if (!array_key_exists('id', $tag) || empty($tag['id'])) {
                                return false;
                            }
                            $tagId = $tag['id'];
                            if (!is_array($tagId)) {
                                $demandAdTag = $this->demandAdTagManager->find($tagId);
                                if ($demandAdTag instanceof VideoDemandAdTagInterface && $demandAdTag->getActive()) {
                                    return $demandAdTag->getName();
                                }
                            }
                        }
                    }, $demandAdTagId);
                }
            }, $currentDemandAdTags);

            $currentDemandAdTags = array_filter($currentDemandAdTags, function ($position) {
                if (!is_array($position)) {
                    return false;
                }

                foreach ($position as $key => $demandAdTag) {
                    if (empty($demandAdTag)) {
                        unset($position[$key]);
                    }
                }

                return !empty($position);
            });
        }

        return array_values($currentDemandAdTags);
    }

    /**
     * @param $waterfallTagAutoOptimizeConfig
     * @param $mappedBy
     * @return array
     */
    protected function getOptimizePositionDemandAdTags($waterfallTagAutoOptimizeConfig, $mappedBy)
    {
        // get preview demandAdTag position
        $optimize = [];

        if (isset($waterfallTagAutoOptimizeConfig['default'])) {
            $optimize = $waterfallTagAutoOptimizeConfig['default'];
        }

        return $this->normalizeDemandAdTags($optimize, $mappedBy);
    }

    /**
     * @param VideoWaterfallTagInterface $waterfallTag
     * @return mixed|void
     * @throws \Exception
     */
    public function updateWaterfallTagCacheWhenAutoOptimizeIntegrationPaused(VideoWaterfallTagInterface $waterfallTag)
    {
        $slotCache = $this->getExistingWaterfallTagCache($waterfallTag);
        if (is_array($slotCache)) {
            if (array_key_exists('autoOptimize', $slotCache)) {
                unset($slotCache['autoOptimize']);
            }
        }

        $nameSpaceCacheKey = sprintf(self::NAMESPACE_CACHE_KEY, $waterfallTag->getUuid());
        $keyGetCurrentVersion = sprintf(self::NAMESPACE_CACHE_KEY_VERSION, $nameSpaceCacheKey);
        $currentVersionForWaterfallTag = (int)self::$redis->get($keyGetCurrentVersion);

        $cacheKey = sprintf('%s[%s][%s]', $nameSpaceCacheKey, self::CACHE_KEY_WATERFALL_TAG, $currentVersionForWaterfallTag);
        try {
            self::$redis->set($cacheKey, $slotCache);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @inheritdoc
     */
    public function getExistingWaterfallTagCache(VideoWaterfallTagInterface $waterfallTag)
    {
        $nameSpaceCacheKey = sprintf(self::NAMESPACE_CACHE_KEY, $waterfallTag->getUuid());
        $keyGetCurrentVersion = sprintf(self::NAMESPACE_CACHE_KEY_VERSION, $nameSpaceCacheKey);
        $currentVersionForWaterfallTag = (int)self::$redis->get($keyGetCurrentVersion);
        $cacheKey = sprintf('%s[%s][%s]', $nameSpaceCacheKey, self::CACHE_KEY_WATERFALL_TAG, $currentVersionForWaterfallTag);
        return self::$redis->get($cacheKey);
    }

    /**
     * @inheritdoc
     */
    public function getOptimizedDemandTagPositionsForWaterfallTag(VideoWaterfallTagInterface $waterfallTag)
    {
        /*
         * input: the waterfallTag cache
         * [
         *     "id": "8e395550-6d62-466d-a6ca-ad17bdd804b8",
         *     "waterfallId": 2,
         *     "platform": [
         *         "flash",
         *     ],
         *     "adDuration": 30,
         *     "waterfall": [
         *         {
         *             "strategy": "parallel",
         *             "demandTags": [
         *                 {
         *                     "id": 2,
         *                     "demandPartner": "test",
         *                     "tagUrl": "http://www.google.com",
         *                     "targeting": []
         *                 }
         *             ]
         *        },
         *        ...
         *    ],
         *    ...,
         *    "autoOptimize": {
         *        "default": [
         *            5,
         *            [2,3],
         *            4
         *       ]
         *    }
         * ]
         */

        /* 1. get current waterfallTag cache, based on waterfallTag */
        $waterfallTagCache = $this->getExistingWaterfallTagCache($waterfallTag);

        if (!is_array($waterfallTagCache)
            || !array_key_exists('waterfall', $waterfallTagCache) || empty($waterfallTagCache['waterfall'])
            || !array_key_exists('autoOptimize', $waterfallTagCache) || empty($waterfallTagCache['autoOptimize'])
        ) {
            return [];
        }

        /* 2. get optimizedPosition cache */
        if (!array_key_exists('autoOptimize', $waterfallTagCache)) {
            return [];
        }
        $autoOptimizedConfig = $waterfallTagCache['autoOptimize'];

        if (!array_key_exists('default', $autoOptimizedConfig)) {
            return [];
        }
        $optimizedDemandAdTagIds = $autoOptimizedConfig['default'];

        if (!is_array($optimizedDemandAdTagIds) || empty($optimizedDemandAdTagIds)) {
            return [];
        }

        /* 3. mapping demandAdTag ids in optimizedPosition cache to waterfallTagItems */
        $waterfallTagItems = [];

        // get and map all demandAdTags of waterfallTag
        $realDemandAdTags = $this->demandAdTagManager->getVideoDemandAdTagsForVideoWaterfallTag($waterfallTag);
        if (!is_array($realDemandAdTags) || empty($realDemandAdTags)) {
            return [];
        }

        $realDemandAdTagsMapping = [];
        foreach ($realDemandAdTags as $realDemandAdTag) {
            if (!$realDemandAdTag instanceof VideoDemandAdTagInterface) {
                continue;
            }

            /** @var VideoDemandAdTagInterface $realDemandAdTag */
            $realDemandAdTagsMapping[$realDemandAdTag->getId()] = $realDemandAdTag;
        }

        // map to waterfallTagItem
        $position = 1;
        /** @var int/array $optimizedDemandAdTagIds */
        foreach ($optimizedDemandAdTagIds as $idOrIds) {
            $videoDemandAdTags = [];

            if (!is_array($idOrIds)) {
                $videoDemandAdTag = $this->getOneDemandAdTag($idOrIds, $realDemandAdTagsMapping);
                if (empty($videoDemandAdTag)) {
                    continue;
                }
                $videoDemandAdTags[] = $videoDemandAdTag;

            } else { // many ad tags in one position
                foreach ($idOrIds as $optimizedDemandAdTag) {
                    if (!is_array($optimizedDemandAdTag)) {
                        $videoDemandAdTag = $this->getOneDemandAdTag($optimizedDemandAdTag, $realDemandAdTagsMapping);
                        if (empty($videoDemandAdTag)) {
                            continue;
                        }
                        $videoDemandAdTags[] = $videoDemandAdTag;
                    } else {
                        if (!array_key_exists('id', $optimizedDemandAdTag) || empty($optimizedDemandAdTag['id'])) {
                            continue;
                        }
                        $optimizedDemandAdTagId = $optimizedDemandAdTag['id'];
                        $videoDemandAdTag = $this->getOneDemandAdTag($optimizedDemandAdTagId, $realDemandAdTagsMapping);
                        if (empty($videoDemandAdTag)) {
                            continue;
                        }
                        if (array_key_exists('weight', $optimizedDemandAdTag)) {
                            $videoDemandAdTag->setRotationWeight($optimizedDemandAdTag['weight']);
                        }
                        $videoDemandAdTags[] = $videoDemandAdTag;
                    }
                }
            }

            // build waterfallTagItem
            $videoDemandAdTags = new ArrayCollection($videoDemandAdTags);
            $waterfallTagItem = (new VideoWaterfallTagItem())
                ->setPosition($position)
                ->setStrategy(VideoWaterfallTagItem::STRATEGY_PARALLELS)
                ->setVideoDemandAdTags($videoDemandAdTags)
                ->setVideoWaterfallTag($waterfallTag);

            // add to waterfallTagItems list
            $waterfallTagItems[] = $waterfallTagItem;

            // increase position for next waterfallTagItem
            $position++;
        }

        /*
         * expected output:
         * [
         *     <waterfallTagItem 1>,
         *     <waterfallTagItem 2>,
         *     <waterfallTagItem 3>
         * ]
         *
         * where <waterfallTagItem 1> is
         * {
         *     "id":1,
         *     "position":1,
         *     "strategy":"parallel",
         *     "videoDemandAdTags":[
         *         {
         *             "id":5,
         *             "priority":0,
         *             "rotationWeight":null,
         *             "active":1,
         *             ...
         *         }
         *     ]
         * }
         */

        /* 4. return result */
        return $waterfallTagItems;
    }

    private function getOneDemandAdTag($optimizedDemandAdTagIds, $realDemandAdTagsMapping)
    {
        // check if in map
        if (!array_key_exists($optimizedDemandAdTagIds, $realDemandAdTagsMapping)) {
            return false;
        }

        // check if active
        /** @var VideoDemandAdTagInterface $videoDemandAdTag */
        $videoDemandAdTag = $realDemandAdTagsMapping[$optimizedDemandAdTagIds];
        if (!$videoDemandAdTag->getActive()) {
            return false;
        }

        return $videoDemandAdTag;
    }

    /**
     * @param VideoWaterfallTagInterface $waterfallTag
     * @param array $autoOptimizedConfig
     * @return mixed|void
     * @throws \Exception
     */
    public function updateAutoOptimizedDataForWaterfallTagCache(VideoWaterfallTagInterface $waterfallTag, array $autoOptimizedConfig)
    {
        $slotCache = $this->getExistingWaterfallTagCache($waterfallTag);
        if (is_array($slotCache)) {
            $slotCache['autoOptimize'] = $autoOptimizedConfig;
        }

        $nameSpaceCacheKey = sprintf(self::NAMESPACE_CACHE_KEY, $waterfallTag->getUuid());
        $keyGetCurrentVersion = sprintf(self::NAMESPACE_CACHE_KEY_VERSION, $nameSpaceCacheKey);
        $currentVersionForWaterfallTag = (int)self::$redis->get($keyGetCurrentVersion);

        $cacheKey = sprintf('%s[%s][%s]', $nameSpaceCacheKey, self::CACHE_KEY_WATERFALL_TAG, $currentVersionForWaterfallTag);
        try {
            self::$redis->set($cacheKey, $slotCache);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @inheritdoc
     */
    public function reorderOptimizeKeyForWaterfallTag(VideoWaterfallTagInterface $waterfallTag, $newVideoWaterfallTagItemOrderIds)
    {
        $newOptimizedDemandAdTagIds = [];
        foreach ($newVideoWaterfallTagItemOrderIds as $newVideoWaterfallTagItemOrderId) {
            if (is_array($newVideoWaterfallTagItemOrderId) && array_key_exists('videoDemandAdTags', $newVideoWaterfallTagItemOrderId)) {

                $newVideoDemandAdTags = $newVideoWaterfallTagItemOrderId['videoDemandAdTags'];

                // remove videoDemandAdTags is paused
                foreach ($newVideoDemandAdTags as $key => $newVideoDemandAdTag) {

                    $videoDemandAdTag = $this->demandAdTagManager->find($newVideoDemandAdTag);

                    if (!$videoDemandAdTag instanceof VideoDemandAdTagInterface) {
                        continue;
                    }

                    if (!$videoDemandAdTag->getActive()) {
                        unset($newVideoDemandAdTags[$key]);
                    }
                }

                if (empty($newVideoDemandAdTags)) {
                    continue;
                }

                $newVideoDemandAdTags = array_values($newVideoDemandAdTags);
                if (is_array($newVideoDemandAdTags) && count($newVideoDemandAdTags) == 1) {
                    $newOptimizedDemandAdTagIds [] = $newVideoDemandAdTags[0];
                    continue;
                }

                $newOptimizedDemandAdTagIds [] = $newVideoDemandAdTags;
            }
        }

        /* re-mapping new order ids with old optimized data from cache */
        $slotCache = $this->getExistingWaterfallTagCache($waterfallTag);
        $oldAutoOptimizedConfig = (is_array($slotCache) && array_key_exists('autoOptimize', $slotCache)) ? $slotCache['autoOptimize'] : [];
        if (!empty($oldAutoOptimizedConfig)) {
            $newAutoOptimizedConfig['default'] = $this->mappingOptimizeCache($oldAutoOptimizedConfig, $newOptimizedDemandAdTagIds);
        } else {
            $newAutoOptimizedConfig = [];
        }

        $this->videoWaterfallTagCacheRefresher->refreshVideoWaterfallTag($waterfallTag, array('autoOptimize' => $newAutoOptimizedConfig));
    }


    /**
     * @param array $oldAutoOptimizedConfig
     * @param array $newOptimizedDemandAdTagIds
     * @return array
     */
    private function mappingOptimizeCache(array $oldAutoOptimizedConfig, array $newOptimizedDemandAdTagIds)
    {
        $oldAutoOptimizedConfig = $oldAutoOptimizedConfig['default'];
        $oldAutoOptimizedConfigMapping = [];
        foreach ($oldAutoOptimizedConfig as $item) {
            // backward compatibility for old cache
            if (!is_array($item)) {
                $oldAutoOptimizedConfigMapping[$item] = [
                    'id' => $item
                ];

                continue;
            }

            // position with only one demand ad tag
            if (array_key_exists('id', $item)) {
                $oldAutoOptimizedConfigMapping[$item['id']] = $item;

                continue;
            }

            // position with multiple demand ad tags
            foreach ($item as $ite) {
                // backward compatibility for old cache
                if (!is_array($ite)) {
                    $oldAutoOptimizedConfigMapping[$ite] = [
                        'id' => $ite
                    ];

                    continue;
                }

                if (!array_key_exists('id', $ite)) {
                    continue;
                }

                $oldAutoOptimizedConfigMapping[$ite['id']] = $ite;
            }
        }

        foreach ($newOptimizedDemandAdTagIds as &$newOptimizedDemandAdTag) {
            if (!is_array($newOptimizedDemandAdTag)) {
                $newOptimizedDemandAdTagId = $newOptimizedDemandAdTag;
                if(array_key_exists('weight', $oldAutoOptimizedConfigMapping[$newOptimizedDemandAdTagId])){
                    $newOptimizedDemandAdTag = [$oldAutoOptimizedConfigMapping[$newOptimizedDemandAdTagId]];
                }else{
                    $newOptimizedDemandAdTag = $oldAutoOptimizedConfigMapping[$newOptimizedDemandAdTagId];
                }
                continue;
            }
            foreach ($newOptimizedDemandAdTag as &$newOptimizedDemandAdTagId) {
                if (!array_key_exists($newOptimizedDemandAdTagId, $oldAutoOptimizedConfigMapping)) {
                    continue;
                }

                $newOptimizedDemandAdTagId = $oldAutoOptimizedConfigMapping[$newOptimizedDemandAdTagId];
            }
            unset($newOptimizedDemandAdTagId);
        }


        unset($newOptimizedDemandAdTag);
        return $newOptimizedDemandAdTagIds;
    }
}