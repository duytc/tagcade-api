<?php


namespace Tagcade\Service\Report\VideoReport\Counter;

use Tagcade\Cache\RedisCacheInterface;
use Tagcade\Domain\DTO\Report\VideoReport\VideoDemandAdTagReportData;
use Tagcade\Domain\DTO\Report\VideoReport\VideoDemandAdTagReportDataHourly;
use Tagcade\Domain\DTO\Report\VideoReport\VideoWaterfallTagReportData;
use Tagcade\Domain\DTO\Report\VideoReport\VideoWaterfallTagReportDataHourly;

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
     * @var RedisCacheInterface
     */
    protected $cache;
    protected $useLocalCache = true;

    /**
     * VideoCacheEventCounter constructor.
     * @param RedisCacheInterface $cache
     */
    public function __construct(RedisCacheInterface $cache)
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
        $hash = !$this->getDataWithDateHour() ? self::REDIS_HASH_VIDEO_EVENT_COUNT : $this->getHashFieldDate() ;

        $results = $supportMGet === true ? $this->cache->hMGet($hash, $cacheKeys) : $this->getSequentiallyMultipleFields($hash, $cacheKeys);

        // make sure that correct key will be returned
        return !$this->getDataWithDateHour()
            ? new VideoWaterfallTagReportData($videoWaterfallTagId, $results, $this->getDate())
            : new VideoWaterfallTagReportDataHourly($videoWaterfallTagId, $results, $this->getDate());
    }

    /**
     * @inheritdoc
     */
    public function getVideoDemandAdTagData($videoDemandAdTagId, $supportMGet = true, $date = null)
    {
        $cacheKeys = $this->createVideoCacheKeyForAdSource($videoDemandAdTagId);
        $hash = !$this->getDataWithDateHour() ? self::REDIS_HASH_VIDEO_EVENT_COUNT : $this->getHashFieldDate();

        $results = $supportMGet === true ? $this->cache->hMGet($hash, $cacheKeys) : $this->getSequentiallyMultipleFields($hash, $cacheKeys);

        // make sure that correct key will be returned
        return !$this->getDataWithDateHour()
            ? new VideoDemandAdTagReportData($videoDemandAdTagId, $results, $this->getDate())
            : new VideoDemandAdTagReportDataHourly($videoDemandAdTagId, $results, $this->getDate());
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagRequestCount($videoWaterfallTagId, $date = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_WATERFALL_AD_TAG, $videoWaterfallTagId);
        $hash = !$this->getDataWithDateHour() ? self::REDIS_HASH_VIDEO_EVENT_COUNT : $this->getHashFieldDate() ;
        return $this->cache->hFetch(
            $hash,
            $this->getCacheKey(static::CACHE_KEY_REQUESTS, $namespace)
        );
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagBidCount($videoWaterfallTagId, $date = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_WATERFALL_AD_TAG, $videoWaterfallTagId);
        $hash = !$this->getDataWithDateHour() ? self::REDIS_HASH_VIDEO_EVENT_COUNT : $this->getHashFieldDate() ;
        return $this->cache->hFetch(
            $hash,
            $this->getCacheKey(static::CACHE_KEY_BIDS, $namespace)
        );
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagErrorCount($videoWaterfallTagId, $date = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_WATERFALL_AD_TAG, $videoWaterfallTagId);
        $hash = !$this->getDataWithDateHour() ? self::REDIS_HASH_VIDEO_EVENT_COUNT : $this->getHashFieldDate() ;
        return $this->cache->hFetch(
            $hash,
            $this->getCacheKey(static::CACHE_KEY_ERRORS, $namespace)
        );
    }


    public function getVideoDemandAdTagImpressionsCount($videoDemandAdTagId, $date = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_DEMAND_AD_TAG, $videoDemandAdTagId);
        $hash = !$this->getDataWithDateHour() ? self::REDIS_HASH_VIDEO_EVENT_COUNT : $this->getHashFieldDate() ;
        return $this->cache->hFetch(
            $hash,
            $this->getCacheKey(static::CACHE_KEY_IMPRESSIONS, $namespace)
        );
    }

    public function getVideoDemandAdTagRequestsCount($videoDemandAdTagId, $date = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_DEMAND_AD_TAG, $videoDemandAdTagId);
        $hash = !$this->getDataWithDateHour() ? self::REDIS_HASH_VIDEO_EVENT_COUNT : $this->getHashFieldDate() ;
        return $this->cache->hFetch(
            $hash,
            $this->getCacheKey(static::CACHE_KEY_REQUESTS, $namespace)
        );
    }

    /**
     * @param $videoDemandAdTagId
     * @return array
     */
    protected function createVideoCacheKeyForAdSource($videoDemandAdTagId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_DEMAND_AD_TAG, $videoDemandAdTagId);
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
        $namespace = $this->getNamespace(self::NAMESPACE_WATERFALL_AD_TAG, $videoWaterfallTagId);

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

    /**
     * @return mixed
     */
    private function getHashFieldDate()
    {
        $date = $this->getDate()->format('ymd');
        //Build new hash field
        return sprintf("%s:%s", self::REDIS_HASH_VIDEO_EVENT_COUNT, $date);
    }
}