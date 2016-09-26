<?php


namespace Tagcade\Service\Report\VideoReport\Counter;

use Tagcade\Cache\Legacy\Cache\RedisArrayCacheInterface;
use Tagcade\Domain\DTO\Report\VideoReport\VideoDemandAdTagReportData;
use Tagcade\Domain\DTO\Report\VideoReport\VideoWaterfallTagReportData;

class VideoCacheEventCounter extends VideoAbstractEventCounter implements VideoCacheEventCounterInterface
{
    /* cache keys */
    const CACHE_KEY_REQUESTS = 'requests';
    const CACHE_KEY_IMPRESSIONS = 'impressions';
    const CACHE_KEY_CLICKS = 'clicks';
    const CACHE_KEY_ERRORS = 'errors';
    const CACHE_KEY_BIDS = 'bids';
    const CACHE_KEY_BLOCKS = 'blocks';

    const REDIS_HASH_VIDEO_EVENT_COUNT = 'video_event_processor:event_count';

    /**
     * @var RedisArrayCacheInterface
     */
    protected $cache;
    protected $useLocalCache = true;

    /**
     * VideoCacheEventCounter constructor.
     * @param RedisArrayCacheInterface $cache
     */
    public function __construct(RedisArrayCacheInterface $cache)
    {
        $this->cache = $cache;
        $this->setDate(new \DateTime('today'));
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagData($videoWaterfallTagId, $supportMGet = true, $date = null)
    {
        $cacheKeys = $this->createVideoCacheKeyForAdTag($videoWaterfallTagId);

        $results = $supportMGet === true ? $this->cache->hMGet(self::REDIS_HASH_VIDEO_EVENT_COUNT, $cacheKeys) : $this->getSequentiallyMultipleFields(self::REDIS_HASH_VIDEO_EVENT_COUNT, $cacheKeys);

        return new VideoWaterfallTagReportData($videoWaterfallTagId, $results, $this->getDate());
    }

    /**
     * @inheritdoc
     */
    public function getVideoDemandAdTagData($videoDemandAdTagId, $supportMGet = true, $date = null)
    {
        $cacheKeys = $this->createVideoCacheKeyForAdSource($videoDemandAdTagId);

        $results = $supportMGet === true ? $this->cache->hMGet(self::REDIS_HASH_VIDEO_EVENT_COUNT, $cacheKeys) : $this->getSequentiallyMultipleFields(self::REDIS_HASH_VIDEO_EVENT_COUNT, $cacheKeys);

        return new VideoDemandAdTagReportData($videoDemandAdTagId, $results, $this->getDate());
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagRequestCount($videoWaterfallTagId, $date = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $videoWaterfallTagId);

        return $this->cache->hFetch(
            self::REDIS_HASH_VIDEO_EVENT_COUNT,
            $this->getCacheKey(static::CACHE_KEY_REQUESTS, $namespace)
        );
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagBidCount($videoWaterfallTagId, $date = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $videoWaterfallTagId);

        return $this->cache->hFetch(
            self::REDIS_HASH_VIDEO_EVENT_COUNT,
            $this->getCacheKey(static::CACHE_KEY_BIDS, $namespace)
        );
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagErrorCount($videoWaterfallTagId, $date = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $videoWaterfallTagId);

        return $this->cache->hFetch(
            self::REDIS_HASH_VIDEO_EVENT_COUNT,
            $this->getCacheKey(static::CACHE_KEY_ERRORS, $namespace)
        );
    }


    public function getVideoDemandAdTagImpressionsCount($videoDemandAdTagId, $date = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_SOURCE, $videoDemandAdTagId);

        return $this->cache->hFetch(
            self::REDIS_HASH_VIDEO_EVENT_COUNT,
            $this->getCacheKey(static::CACHE_KEY_IMPRESSIONS, $namespace)
        );
    }


    /**
     * @param $videoDemandAdTagId
     * @return array
     */
    protected function createVideoCacheKeyForAdSource($videoDemandAdTagId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_SOURCE, $videoDemandAdTagId);

        return array (
            $this->getCacheKey(self::CACHE_KEY_REQUESTS, $namespace),
            $this->getCacheKey(self::CACHE_KEY_IMPRESSIONS, $namespace),
            $this->getCacheKey(self::CACHE_KEY_CLICKS, $namespace),
            $this->getCacheKey(self::CACHE_KEY_ERRORS, $namespace),
            $this->getCacheKey(self::CACHE_KEY_BIDS, $namespace),
            $this->getCacheKey(self::CACHE_KEY_BLOCKS, $namespace)
        );
    }

    /**
     * @param $videoWaterfallTagId
     * @return array
     */
    protected function createVideoCacheKeyForAdTag($videoWaterfallTagId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $videoWaterfallTagId);

        return array (
            $this->getCacheKey(self::CACHE_KEY_REQUESTS, $namespace),
            $this->getCacheKey(self::CACHE_KEY_IMPRESSIONS, $namespace),
            $this->getCacheKey(self::CACHE_KEY_CLICKS, $namespace),
            $this->getCacheKey(self::CACHE_KEY_ERRORS, $namespace),
            $this->getCacheKey(self::CACHE_KEY_BIDS, $namespace),
            $this->getCacheKey(self::CACHE_KEY_BLOCKS,$namespace)
        );
    }

    /**
     * @inheritdoc
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * get multiple fields sequentially from cache in case of not support mGet for multiple-redis-server-instances
     * this takes more time when get many records from cache but makes an exactly report
     *
     * @param $hash
     * @param array $fields
     * @return array
     */
    private function getSequentiallyMultipleFields($hash, array $fields)
    {
        $results = [];
        foreach($fields as $field) {
            $results[] = $this->cache->hFetch($hash, $field);
        }

        return $results;
    }
}