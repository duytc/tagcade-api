<?php

namespace Tagcade\Service\Report\VideoReport\Counter;

use Redis;
use Tagcade\Cache\RedisCache;

class SnapshotVideoCacheEventCounter implements SnapshotVideoCacheEventCounterInterface
{
    /** @var VideoCacheEventCounter $videoCacheEventCounter */
    private $videoCacheEventCounter;

    /** @var integer */
    private $cacheKeyTimeOut;

    /**
     * SnapshotVideoCacheEventCounter constructor.
     * @param VideoCacheEventCounter $videoCacheEventCounter
     * @param $cacheKeyTimeOut
     */
    public function __construct(VideoCacheEventCounter $videoCacheEventCounter, $cacheKeyTimeOut)
    {
        $this->videoCacheEventCounter = $videoCacheEventCounter;
        $this->cacheKeyTimeOut = $cacheKeyTimeOut;
    }

    /**
     * @inheritdoc
     */
    public function snapshotDemandAdTag($videoDemandAdTagId, $postFix)
    {
        $videoDemandAdTagData = $this->videoCacheEventCounter->getVideoDemandAdTagData($videoDemandAdTagId, true);

        // save new key Request for demand Adtag
        $this->saveNewKeyForDemandTag($videoDemandAdTagId, VideoCacheEventCounter::CACHE_KEY_REQUESTS, $videoDemandAdTagData->getRequests(), $postFix);

        // save new key impression for demand Adtag
        $this->saveNewKeyForDemandTag($videoDemandAdTagId, VideoCacheEventCounter::CACHE_KEY_IMPRESSIONS, $videoDemandAdTagData->getImpressions(), $postFix);

        // save new key birds for demand Adtag
        $this->saveNewKeyForDemandTag($videoDemandAdTagId, VideoCacheEventCounter::CACHE_KEY_BIDS, $videoDemandAdTagData->getBids(), $postFix);

        // save new key click for demand Adtag
        $this->saveNewKeyForDemandTag($videoDemandAdTagId, VideoCacheEventCounter::CACHE_KEY_CLICKS, $videoDemandAdTagData->getClicks(), $postFix);

        // save new key errors for demand Adtag
        $this->saveNewKeyForDemandTag($videoDemandAdTagId, VideoCacheEventCounter::CACHE_KEY_ERRORS, $videoDemandAdTagData->getErrors(), $postFix);

        // save new key block for demand Adtag
        $this->saveNewKeyForDemandTag($videoDemandAdTagId, VideoCacheEventCounter::CACHE_KEY_BLOCKS, $videoDemandAdTagData->getBlocks(), $postFix);

    }

    /**
     * @inheritdoc
     */
    public function snapshotWaterfallTag($videoWaterfallTagId, $postFix)
    {
        $videoWaterfallTagData = $this->videoCacheEventCounter->getVideoWaterfallTagData($videoWaterfallTagId, true);

        // save new key Request for WaterfallTag
        $this->saveNewKeyForWaterfallTag($videoWaterfallTagId, VideoCacheEventCounter::CACHE_KEY_REQUESTS, $videoWaterfallTagData->getRequests(), $postFix);

        // save new key birds WaterfallTag
        $this->saveNewKeyForWaterfallTag($videoWaterfallTagId, VideoCacheEventCounter::CACHE_KEY_BIDS, $videoWaterfallTagData->getBids(), $postFix);

        // save new key errors for WaterfallTag
        $this->saveNewKeyForWaterfallTag($videoWaterfallTagId, VideoCacheEventCounter::CACHE_KEY_ERRORS, $videoWaterfallTagData->getErrors(), $postFix);
    }

    /**
     * @param $videoWaterfallTagId
     * @param $constant
     * @param $value
     * @param $postFix
     */
    private function saveNewKeyForWaterfallTag($videoWaterfallTagId, $constant, $value, $postFix)
    {
        $namespace = $this->videoCacheEventCounter->getNamespace(VideoCacheEventCounter::NAMESPACE_WATERFALL_AD_TAG, $videoWaterfallTagId);
        $cacheKey = $this->videoCacheEventCounter->getCacheKey($constant, $namespace);

        //Build new key
        $snapShotCacheKey = sprintf("%s%s", $cacheKey, $postFix);

        $this->saveToRedis($snapShotCacheKey, $value);
    }

    /**
     * @param $videoDemandAdTagId
     * @param $constant
     * @param $value
     * @param $postFix
     */
    private function saveNewKeyForDemandTag($videoDemandAdTagId, $constant, $value, $postFix)
    {
        $namespace = $this->videoCacheEventCounter->getNamespace(VideoCacheEventCounter::NAMESPACE_DEMAND_AD_TAG, $videoDemandAdTagId);
        $cacheKey = $this->videoCacheEventCounter->getCacheKey($constant, $namespace);

        //Build new key
        $snapShotCacheKey = sprintf("%s%s", $cacheKey, $postFix);

        $this->saveToRedis($snapShotCacheKey, $value);
    }

    /**
     * @param $key
     * @param $value
     */
    private function saveToRedis($key, $value)
    {
        $cache = $this->videoCacheEventCounter->getCache();
        if ($cache instanceof RedisCache || $cache instanceof \Tagcade\Cache\Legacy\Cache\RedisCache) {
            $redis = $cache->getRedis();
            $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);

            $cache->hSave(
                $hash = $this->getHashFieldDate(VideoCacheEventCounter::REDIS_HASH_VIDEO_EVENT_COUNT),
                $key,
                $value
            );
        }
    }

    /**
     * @param $hashField
     * @return mixed
     */
    private function getHashFieldDate($hashField)
    {
        $date = $this->videoCacheEventCounter->getDate()->format('ymd');
        //Build new hash field
        return sprintf("%s:%s", $hashField, $date);
    }

    public function setExpiredTimeForHashFieldDate()
    {
        $cache = $this->videoCacheEventCounter->getCache();
        if ($cache instanceof RedisCache || $cache instanceof \Tagcade\Cache\Legacy\Cache\RedisCache) {
            $redis = $cache->getRedis();
            $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);

            $cache->expire($this->getHashFieldDate(VideoCacheEventCounter::REDIS_HASH_VIDEO_EVENT_COUNT), $this->cacheKeyTimeOut);
        }
    }
}