<?php

namespace Tagcade\Service\Report\RtbReport\Counter;

use Tagcade\Cache\Legacy\Cache\RedisArrayCacheInterface;
use Tagcade\Domain\DTO\Report\RtbReport\RtbAdSlotReportCount;
use Tagcade\Domain\DTO\Report\RtbReport\RtbRonAdSlotReportCount;

class RtbCacheEventCounter extends RtbAbstractEventCounter implements RtbCacheEventCounterInterface
{
    const KEY_DATE_FORMAT = 'ymd';
    const SEGMENT_NONE = null;

    /* cache keys */
    const CACHE_KEY_SLOT_OPPORTUNITY = 'opportunity'; // same "opportunities" key, used with different namespace
    const CACHE_KEY_IMPRESSION = 'impression';
    const CACHE_KEY_PRICE = 'price';

    /* namespace keys */
    const NAMESPACE_AD_SLOT = 'adslot_%d';
    const NAMESPACE_ACCOUNT = 'account_%d';
    const NAMESPACE_RON_AD_SLOT = 'ron_adslot_%d';
    const NAMESPACE_SEGMENT = 'segment_%d';

    const REDIS_HASH_RTB_EVENT_COUNT = 'rtb_event_processor:event_count';

    /**
     * @var RedisArrayCacheInterface
     */
    protected $cache;

    protected $formattedDate;
    protected $useLocalCache = true;

    public function __construct(RedisArrayCacheInterface $cache)
    {
        $this->cache = $cache;
        $this->setDate(new \DateTime('today'));
    }

    /**
     * @inheritdoc
     */
    public function setDate(\DateTime $date = null)
    {
        if (!$date) {
            $date = new \DateTime('today');
        }

        $this->date = $date;
        $this->formattedDate = $date->format(self::KEY_DATE_FORMAT);
    }

    /**
     * @inheritdoc
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @inheritdoc
     */
    public function getRtbAdSlotReport($adSlotId, $supportMGet = true)
    {
        $rtbCacheKeys = $this->createRtbCacheKeysForAdSlot($adSlotId);

        $results = $supportMGet === true ? $this->cache->hMGet(self::REDIS_HASH_RTB_EVENT_COUNT, $rtbCacheKeys) : $this->getSequentiallyMultipleFields(self::REDIS_HASH_RTB_EVENT_COUNT, $rtbCacheKeys);

        return new RtbAdSlotReportCount($adSlotId, $results, $supportMGet, $this->getDate());
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
     * @inheritdoc
     */
    public function getRtbAdSlotReports(array $adSlotIds, $supportMGet = true)
    {
        /* build cache keys for all adSlotIds */
        $rtbCacheKeys = [];
        foreach ($adSlotIds as $id) {
            $tmpCacheKeys = $this->createRtbCacheKeysForAdSlot($id);

            foreach ($tmpCacheKeys as $key) {
                $rtbCacheKeys[] = $key; // note: should not use array_merge to reduce overhead of function call
            }
        }

        /* get cache data for all adSlotIds */
        $results = $supportMGet === true ? $this->cache->hMGet(self::REDIS_HASH_RTB_EVENT_COUNT, $rtbCacheKeys) : $this->getSequentiallyMultipleFields(self::REDIS_HASH_RTB_EVENT_COUNT, $rtbCacheKeys);

        /* build reports from data */
        $reports = [];
        foreach ($adSlotIds as $id) {
            $reports[] = new RtbAdSlotReportCount($id, $results, $supportMGet, $this->getDate());
        }

        return $reports;
    }

    /**
     * @inheritdoc
     */
    public function getRtbRonAdSlotReport($ronAdSlotId, $segmentId = null, $supportMGet = true)
    {
        $rtbCacheKeys = $this->createRtbCacheKeyForRonAdSlot($ronAdSlotId, $segmentId);

        $results = $supportMGet === true ? $this->cache->hMGet(self::REDIS_HASH_RTB_EVENT_COUNT, $rtbCacheKeys) : $this->getSequentiallyMultipleFields(self::REDIS_HASH_RTB_EVENT_COUNT, $rtbCacheKeys);

        return new RtbRonAdSlotReportCount($ronAdSlotId, $results, $this->getDate(), $segmentId);
    }

    /**
     * @inheritdoc
     */
    public function getRtbRonAdSlotReports(array $ronAdSlotIds, $segmentId = null, $supportMGet = true)
    {
        /* build cache keys for all ron adSlotIds */
        $rtbCacheKeys = [];
        foreach ($ronAdSlotIds as $id) {
            $tmpCacheKeys = $this->createRtbCacheKeyForRonAdSlot($id, $segmentId);

            foreach ($tmpCacheKeys as $key) {
                $rtbCacheKeys[] = $key; // note: should not use array_merge to reduce overhead of function call
            }
        }

        /* get cache data for all adSlotIds */
        $results = $supportMGet === true ? $this->cache->hMGet(self::REDIS_HASH_RTB_EVENT_COUNT, $rtbCacheKeys) :  $this->getSequentiallyMultipleFields(self::REDIS_HASH_RTB_EVENT_COUNT, $rtbCacheKeys);

        /* build reports from data */
        $reports = [];
        foreach ($ronAdSlotIds as $id) {
            $reports[] = new RtbRonAdSlotReportCount($id, $results, $this->getDate(), $segmentId);
        }

        return $reports;
    }

    /**
     * create Rtb Cache Keys For AdSlot, includes slot_opportunity, impression and price
     *
     * @param $adSlotId
     * @return array
     */
    protected function createRtbCacheKeysForAdSlot($adSlotId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_SLOT, $adSlotId);

        return array (
            $this->getCacheKey(self::CACHE_KEY_SLOT_OPPORTUNITY, $namespace),
            $this->getCacheKey(self::CACHE_KEY_IMPRESSION, $namespace),
            $this->getCacheKey(self::CACHE_KEY_PRICE, $namespace)
        );
    }

    /**
     * @param $ronAdSlotId
     * @param null $segmentId
     * @return array
     */
    protected function createRtbCacheKeyForRonAdSlot($ronAdSlotId, $segmentId = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_RON_AD_SLOT, $ronAdSlotId, self::NAMESPACE_SEGMENT, $segmentId);

        return array (
            $this->getCacheKey(self::CACHE_KEY_SLOT_OPPORTUNITY, $namespace),
            $this->getCacheKey(self::CACHE_KEY_IMPRESSION, $namespace),
            $this->getCacheKey(self::CACHE_KEY_PRICE, $namespace)
        );
    }

    /**
     * get Namespace from namespaceFormat and id, optional with appendingFormat and appendingId
     *
     * @param $namespaceFormat
     * @param $id
     * @param null $appendingFormat
     * @param null $appendingId
     * @return string
     */
    public function getNamespace($namespaceFormat, $id, $appendingFormat = null, $appendingId = null)
    {
        $namespace = sprintf($namespaceFormat, $id);

        return $this->appendNamespace($namespace, $appendingFormat, $appendingId);
    }

    /**
     * Does append namespace if id != null. Otherwise returning original namespace
     * @param $namespace
     * @param string|null $appendFormat
     * @param int|null $id
     * @return string
     */
    protected function appendNamespace($namespace, $appendFormat = null, $id = null)
    {
        return (null !== $id && null !== $appendFormat) ? sprintf($namespace . ':' . $appendFormat, $id) : $namespace;
    }

    /**
     * @inheritdoc
     */
    public function getCacheKey($key, $namespace)
    {
        $keyFormat = '%s:%s:%s';
        return sprintf($keyFormat, $key, $namespace, $this->formattedDate);
    }
}