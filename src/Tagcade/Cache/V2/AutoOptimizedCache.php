<?php

namespace Tagcade\Cache\V2;

use Doctrine\Common\Collections\Collection;
use Redis;
use Tagcade\Cache\V2\Refresher\AdSlotCacheInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Domain\DTO\Core\AutoOptimizeCacheParam;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Service\AutoOptimizeConfigGenerator;

class AutoOptimizedCache implements AutoOptimizedCacheInterface
{
    const REDIS_CONNECT_TIMEOUT_IN_SECONDS = 2;
    const NAMESPACE_CACHE_KEY = 'tagcade_adslot_v2_%d';
    const CACHE_KEY_AD_SLOT = 'all_tags_array';
    const NAMESPACE_CACHE_KEY_VERSION = 'TagcadeNamespaceCacheKey[%s]';
    /**
     * @var Redis
     */
    static private $redis;

    private $host;

    private $port;

    /** @var AdTagManagerInterface */
    private $adTagManager;

    /** @var AdSlotManagerInterface */
    private $adSlotManager;

    /** @var  AutoOptimizeConfigGenerator */
    private $autoOptimizeConfigGenerator;

    /** @var AdSlotCacheInterface */
    private $adSlotCache;

    /**
     * AutoOptimizedCache constructor.
     * @param $host
     * @param $port
     * @param AdTagManagerInterface $adTagManager
     * @param AdSlotManagerInterface $adSlotManager
     * @param AutoOptimizeConfigGenerator $autoOptimizeConfigGenerator
     * @param AdSlotCacheInterface $adSlotCache
     */
    public function __construct($host, $port, AdTagManagerInterface $adTagManager, AdSlotManagerInterface $adSlotManager,
                                AutoOptimizeConfigGenerator $autoOptimizeConfigGenerator, AdSlotCacheInterface $adSlotCache)
    {
        $this->host = $host;
        $this->port = $port;

        $this->getRedis();

        $this->adTagManager = $adTagManager;
        $this->adSlotManager = $adSlotManager;
        $this->adSlotCache = $adSlotCache;
        $this->autoOptimizeConfigGenerator = $autoOptimizeConfigGenerator;
    }

    /**
     * @param $slotId
     * @return bool|string
     */
    protected function getSlotCache($slotId)
    {
        $this->getRedis();

        $nameSpaceCacheKey = sprintf(self::NAMESPACE_CACHE_KEY, $slotId);
        $keyGetCurrentVersion = sprintf(self::NAMESPACE_CACHE_KEY_VERSION, $nameSpaceCacheKey);
        $currentVersionForAdSlot = (int)self::$redis->get($keyGetCurrentVersion);

        $cacheKey = sprintf('%s[%s][%s]', $nameSpaceCacheKey, self::CACHE_KEY_AD_SLOT, $currentVersionForAdSlot);
        return self::$redis->get($cacheKey);
    }

    /**
     * @param AutoOptimizeCacheParam $param
     * @return mixed|void
     */
    public function updateCacheForAdSlots(AutoOptimizeCacheParam $param)
    {
        $adSlotAutoOptimizeConfigs = $this->autoOptimizeConfigGenerator->generate($param);
        $adSlots = $param->getAdSlots();

        foreach ($adSlots as $adSlot) {
            $slot = $this->adSlotManager->find($adSlot);

            if (!$slot instanceof BaseAdSlotInterface) {
                continue;
            }

            if (!$slot->isAutoOptimize()) {
                $this->updateAutoOptimizedConfigForAdSlot($adSlot, []);
                continue;
            }

            if (array_key_exists($adSlot, $adSlotAutoOptimizeConfigs)) {
                $this->updateAutoOptimizedConfigForAdSlot($adSlot, $adSlotAutoOptimizeConfigs[$adSlot]);
            }
        }
    }

    /**
     * @param AutoOptimizeCacheParam $param
     * @return mixed|void
     */
    public function getPreviewPositionForAdSlots(AutoOptimizeCacheParam $param)
    {
        $adSlotAutoOptimizeConfigs = $this->autoOptimizeConfigGenerator->generate($param);
        $adSlots = $param->getAdSlots();
        $previewPositionResult = [];

        foreach ($adSlots as $adSlot) {
            $adSlot = $this->adSlotManager->find($adSlot);

            if (!$adSlot instanceof BaseAdSlotInterface) {
                continue;
            }
            // get current version cache
            $currentPosition = $this->getCurrentPositionAdTags($adSlot, $param->getMappedBy());
            $optimizePosition = $this->getOptimizePositionAdTags($adSlotAutoOptimizeConfigs[$adSlot->getId()], $param->getMappedBy());

            $key = sprintf("%s (ID: %s)", $adSlot->getId(), $adSlot->getId());
            $previewPositionResult[$key]['current'] = $currentPosition;
            $previewPositionResult[$key]['optimize'] = $optimizePosition;
        }

        return $previewPositionResult;
    }

    /**
     * @param BaseAdSlotInterface $adSlot
     * @param $mappedBy
     * @return array
     */
    protected function getCurrentPositionAdTags(BaseAdSlotInterface $adSlot, $mappedBy)
    {
        // get preview adTag position
        $currentAdTags = [];
        $currentSlotCache = $this->getSlotCache($adSlot->getId());


        if (isset($currentSlotCache['autoOptimize']['default']) && !empty($currentSlotCache['autoOptimize']['default'])) {
            $currentAdTags = $currentSlotCache['autoOptimize']['default'];
        } else {
            if (isset($currentSlotCache['tags']) && !empty($currentSlotCache['tags'])) {
                $adTags = $currentSlotCache['tags'];
                $adTags = $adTags instanceof Collection ? $adTags->toArray() : $adTags;
                foreach ($adTags as $adTag) {
                    $currentAdTags[] = $adTag['id'];
                }
            } else {
                $adTags = $adSlot->getAdTags();
                $adTags = $adTags instanceof Collection ? $adTags->toArray() : $adTags;
                foreach ($adTags as $adTag) {
                    if (!$adTag instanceof AdTagInterface) {
                        continue;
                    }
                    $currentAdTags[$adTag->getPosition()][] = $adTag->getId();
                }
            }

        }

        return $this->normalizeAdTags($currentAdTags, $mappedBy);
    }

    /**
     * @param $adSlotAutoOptimizeConfig
     * @param $mappedBy
     * @return array
     */
    protected function getOptimizePositionAdTags($adSlotAutoOptimizeConfig, $mappedBy)
    {
        // get preview adTag position
        $optimize = [];

        if (isset($adSlotAutoOptimizeConfig['default'])) {
            $optimize = $adSlotAutoOptimizeConfig['default'];
        }

        return $this->normalizeAdTags($optimize, $mappedBy);
    }

    /**
     * @param $slotId
     * @param array $autoOptimizedConfig
     * @return bool|mixed
     */
    public function updateAutoOptimizedConfigForAdSlot($slotId, array $autoOptimizedConfig)
    {
        $adSlot = $this->adSlotManager->find($slotId);

        if (!$adSlot instanceof DisplayAdSlotInterface) {
            return false;
        }

        $this->adSlotCache->refreshCacheForDisplayAdSlot($adSlot, true, array('autoOptimize' => $autoOptimizedConfig));

        return true;
    }

    /**
     * @param $slotId
     * @return bool|mixed|string
     */
    public function getExistingAdSlotCache($slotId)
    {
        $nameSpaceCacheKey = sprintf(self::NAMESPACE_CACHE_KEY, $slotId);
        $keyGetCurrentVersion = sprintf(self::NAMESPACE_CACHE_KEY_VERSION, $nameSpaceCacheKey);
        $currentVersionForAdSlot = (int)self::$redis->get($keyGetCurrentVersion);

        $cacheKey = sprintf('%s[%s][%s]', $nameSpaceCacheKey, self::CACHE_KEY_AD_SLOT, $currentVersionForAdSlot);
        return self::$redis->get($cacheKey);
    }

    /**
     * @param DisplayAdSlotInterface $adSlot
     * @return mixed|void
     * @throws \Exception
     * @throws \RedisException
     */
    public function updateAdSlotCacheWhenAutoOptimizeIntegrationPaused(DisplayAdSlotInterface $adSlot)
    {
        $slotCache = $this->getExistingAdSlotCache($adSlot->getId());
        if (is_array($slotCache)) {
            if (array_key_exists('autoOptimize', $slotCache)) {
                unset($slotCache['autoOptimize']);
            }
        }

        $nameSpaceCacheKey = sprintf(self::NAMESPACE_CACHE_KEY, $adSlot->getId());
        $keyGetCurrentVersion = sprintf(self::NAMESPACE_CACHE_KEY_VERSION, $nameSpaceCacheKey);
        $currentVersionForAdSlot = (int)self::$redis->get($keyGetCurrentVersion);

        $cacheKey = sprintf('%s[%s][%s]', $nameSpaceCacheKey, self::CACHE_KEY_AD_SLOT, $currentVersionForAdSlot);
        try {
            self::$redis->set($cacheKey, $slotCache);
        } catch (\RedisException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function getOptimizedAdTagPositionsForAdSlotBySegmentsValue($adSlotId, $countryValue = '', $domainValue = '', $browserValue = '')
    {
        $byArray = [];
        if (!empty($countryValue)) $byArray [] = 'country';
        if (!empty($domainValue)) $byArray [] = 'domain';
        if (!empty($browserValue)) $byArray [] = 'browser';
        $by = '';

        if (!empty($byArray)) {
            sort($byArray); // sort by alphabet (this is very helpful in serve ads, ui, scoring service and api

            // get value to compare
            $valuesToGet = '';
            foreach ($byArray as $item) {
                $dot = empty($valuesToGet) ? "" : ".";
                if ($item == 'country' && !empty($countryValue) && $countryValue != 'global') {
                    $valuesToGet = $valuesToGet . $dot . $countryValue;
                    $by = $by . $dot . $item;
                }
                if ($item == 'domain' && !empty($domainValue) && $domainValue != 'global') {
                    $valuesToGet = $valuesToGet . $dot . $domainValue;
                    $by = $by . $dot . $item;

                }
                if ($item == 'browser' && !empty($browserValue) && $browserValue == 'global') {
                    $valuesToGet = $valuesToGet . $dot . $browserValue;
                    $by = $by . $dot . $item;
                }
            }

            unset($byArray);
        } else {
            return [];
        }

        // get adSlot cache based on adSlotId
        $slotCache = $this->getExistingAdSlotCache($adSlotId);

        if ($slotCache == false) {
            return [];
        }

        if (!array_key_exists('tags', $slotCache) || empty($slotCache['tags'])) {
            return [];
        }

        if (!array_key_exists('autoOptimize', $slotCache) || empty($slotCache['autoOptimize'])) {
            return [];
        }

        $adTags = $slotCache['tags'];
        $adSlot = $this->adSlotManager->find($adSlotId);
        if (!$adSlot instanceof BaseAdSlotInterface) {
            return [];
        }

        $realAdTags = $adSlot->getAdTags();
        $realAdTags = $realAdTags instanceof Collection ? $realAdTags->toArray() : $realAdTags;
        $pausedAdTags = array_filter($realAdTags, function ($adTag) {
            return ($adTag instanceof AdTagInterface) && !$adTag->isActive();
        });

        foreach ($pausedAdTags as $adTag) {
            if (!$adTag instanceof AdTagInterface) {
                continue;
            }

            $adTags[] = [
                'id' => $adTag->getId(),
                'tag' => $adTag->getName()
            ];
        }
        // store all adtags to use for optimized position
        $newAdTags = [];
        foreach ($adTags as $adTag) {
            // single tag in one position
            if (array_key_exists('id', $adTag)) {
                $adTagEntity = $this->adTagManager->find($adTag['id']);
                if (!$adTagEntity instanceof AdTagInterface) {
                    continue;
                }

                $newAdTags[$adTag['id']] = $adTagEntity;
                unset($adTagEntity);
                continue;
            }

            // multiple tags in same position
            foreach ($adTag as $item) {
                if (array_key_exists('id', $item)) {
                    $adTagEntity = $this->adTagManager->find($item['id']);
                    if (!$adTagEntity instanceof AdTagInterface) {
                        continue;
                    }

                    if ($adTagEntity->isActive() == false) {
                        continue;
                    }

                    $newAdTags[$item['id']] = $adTagEntity;
                    unset($adTagEntity);
                }
            }
        }

        $autoOptimizedConfig = $slotCache['autoOptimize'];
        unset($slotCache, $adTags);
        // get list of Ad Tag Ids has been optimized position based on $by and its values
        if (array_key_exists($by, $autoOptimizedConfig)) {
            $listOfValues = $autoOptimizedConfig[$by];

            if (array_key_exists($valuesToGet, $listOfValues)) {
                $listOfAdTagIds = $listOfValues[$valuesToGet];
            } else {
                // if sub segment field has global
                if (is_array($listOfValues) && array_key_exists('default', $listOfValues)) {
                    $listOfAdTagIds = $listOfValues['default'];
                } else {
                    // get global default
                    $listOfAdTagIds = $autoOptimizedConfig['default'];
                }
            }
        } else {
            // get global default
            $listOfAdTagIds = $autoOptimizedConfig['default'];
        }

        // $listOfAdTagIds = 1, [2,3], 4
        $newAdTagsPosition = [];
        $position = 1;
        foreach ($listOfAdTagIds as $listOfAdTagId) {
            if (is_array($listOfAdTagId)) {
                $newSubItem = [];
                foreach ($listOfAdTagId as $valueItem) {
                    if (array_key_exists($valueItem, $newAdTags)) {
                        $adTagItem = $newAdTags[$valueItem];
                        if (!$adTagItem instanceof AdTagInterface) {
                            continue;
                        }

                        $adTagItem->setPosition($position);
                        $newSubItem [] = $adTagItem;
                    }
                }

                $newAdTagsPosition [] = $newSubItem;
                $position++;
                unset($valueItem, $newSubItem);
            } else {
                if (array_key_exists($listOfAdTagId, $newAdTags)) {
                    $adTagItem = $newAdTags[$listOfAdTagId];
                    if (!$adTagItem instanceof AdTagInterface) {
                        continue;
                    }

                    $adTagItem->setPosition($position);
                    $newAdTagsPosition [] = $newAdTags[$listOfAdTagId];
                    $position++;
                }
            }
        }

        /*
         * cache
         *  "autoOptimize": {
         *      "country": {
         *          "us": [1, [2, 3], 4]
         *      },
         *      "default": [1, [2, 3], 4]
         *  }
         * expect: if $by = country and value = us
         *
         *          [
         *              {
         *                  "id": "1",
         *                  "tag": "tag1"
         *
         *                  //adTagEntity
         *              },
         *              [
         *                  {
         *                      "id": "2",
         *                      "name": "tag2"
         *                  },
         *                  {
         *                      "id": "3",
         *                      "tag": "tag3"
         *                  }
         *              ],
         *              {
         *                  "id": "4",
         *                  "tag": "tag4"
         *              }
         *              ]
         *
         *
         */

        return $newAdTagsPosition;
    }

    /**
     * @param DisplayAdSlotInterface $adSlot
     * @param array $autoOptimizedConfig
     * @return mixed|void
     * @throws \Exception
     * @throws \RedisException
     */
    public function updateAutoOptimizedDataForAdSlotCache(DisplayAdSlotInterface $adSlot, array $autoOptimizedConfig)
    {
        $slotCache = $this->getExistingAdSlotCache($adSlot->getId());
        if (is_array($slotCache)) {
            $slotCache['autoOptimize'] = $autoOptimizedConfig;
        }

        $nameSpaceCacheKey = sprintf(self::NAMESPACE_CACHE_KEY, $adSlot->getId());
        $keyGetCurrentVersion = sprintf(self::NAMESPACE_CACHE_KEY_VERSION, $nameSpaceCacheKey);
        $currentVersionForAdSlot = (int)self::$redis->get($keyGetCurrentVersion);

        $cacheKey = sprintf('%s[%s][%s]', $nameSpaceCacheKey, self::CACHE_KEY_AD_SLOT, $currentVersionForAdSlot);
        try {
            self::$redis->set($cacheKey, $slotCache);
        } catch (\RedisException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @param $currentAdTags
     * @param $mappedBy
     * @return array
     */
    private function normalizeAdTags($currentAdTags, $mappedBy)
    {
        $currentAdTags = array_map(function ($item) {
            if (!empty($item)) {
                return is_array($item) ? $item : [$item];
            }
        }, $currentAdTags);

        if ($mappedBy == 'adTagName') {
            $currentAdTags = array_map(function ($adTagId) {
                if (!is_array($adTagId)) {
                    $adTag = $this->adTagManager->find($adTagId);
                    if ($adTag instanceof AdTagInterface && $adTag->isActive()) {
                        return $adTag->getName();
                    }
                } else {
                    return array_map(function ($tagId) {
                        if (!is_array($tagId)) {
                            $adTag = $this->adTagManager->find($tagId);
                            if ($adTag instanceof AdTagInterface && $adTag->isActive()) {
                                return $adTag->getName();
                            }
                        }
                    }, $adTagId);
                }
            }, $currentAdTags);

            $currentAdTags = array_filter($currentAdTags, function ($position) {
                if (!is_array($position)) {
                    return false;
                }

                foreach ($position as $key => $adTag) {
                    if (empty($adTag)) {
                        unset($position[$key]);
                    }
                }

                return !empty($position);
            });
        }

        return array_values($currentAdTags);
    }

    /**
     * @return Redis
     */
    private function getRedis()
    {
        if (!self::$redis instanceof Redis) {
            self::$redis = new Redis();

            try {
                self::$redis->connect($this->host, $this->port, self::REDIS_CONNECT_TIMEOUT_IN_SECONDS);
                self::$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
            } catch (\RedisException $e) {
                // todo refactor to check if redis is connected or not
                self::$redis = null;
            }
        }

        return self::$redis;
    }
}