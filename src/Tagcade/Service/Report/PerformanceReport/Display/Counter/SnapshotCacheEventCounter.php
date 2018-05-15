<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Counter;

use Redis;
use Tagcade\Cache\RedisCache;

class SnapshotCacheEventCounter implements SnapshotCacheEventCounterInterface
{
    /** @var CacheEventCounterInterface */
    private $cacheEventCounter;

    /** @var integer */
    private $cacheKeyTimeOut;

    /**
     * SnapshotCacheEventCounter constructor.
     * @param CacheEventCounterInterface $cacheEventCounter
     * @param $cacheKeyTimeOut
     */
    public function __construct(CacheEventCounterInterface $cacheEventCounter, $cacheKeyTimeOut)
    {
        $this->cacheEventCounter = $cacheEventCounter;
        $this->cacheKeyTimeOut = $cacheKeyTimeOut;
    }

    /**
     * @inheritdoc
     */
    public function snapshotRefreshesCount($adTagId, $postFix)
    {
        $refreshes = $this->cacheEventCounter->getRefreshesCount($adTagId);

        $this->saveNewKeyForAdTag($adTagId, CacheEventCounter::CACHE_KEY_REFRESHES, $refreshes, $postFix);
    }

    /**
     * @inheritdoc
     */
    public function snapshotBlankImpressionCount($adTagId, $postFix)
    {
        $blackImpressions = $this->cacheEventCounter->getBlankImpressionCount($adTagId);

        $this->saveNewKeyForAdTag($adTagId, CacheEventCounter::CACHE_KEY_BLANK_IMPRESSION, $blackImpressions, $postFix);
    }

    /**
     * @inheritdoc
     */
    public function snapshotVoidImpressionCount($adTagId, $postFix)
    {
        $voidImpressions = $this->cacheEventCounter->getVoidImpressionCount($adTagId);

        $this->saveNewKeyForAdTag($adTagId, CacheEventCounter::CACHE_KEY_VOID_IMPRESSION, $voidImpressions, $postFix);
    }

    /**
     * @inheritdoc
     */
    public function snapshotOpportunitiesCount($adTagId, $postFix)
    {
        $opportunities = $this->cacheEventCounter->getOpportunityCount($adTagId);

        $this->saveNewKeyForAdTag($adTagId, CacheEventCounter::CACHE_KEY_OPPORTUNITY, $opportunities, $postFix);
    }

    /**
     * @inheritdoc
     */
    public function snapshotClicksCount($adTagId, $postFix)
    {
        $clicks = $this->cacheEventCounter->getClickCount($adTagId);

        $this->saveNewKeyForAdTag($adTagId, CacheEventCounter::CACHE_KEY_CLICK, $clicks, $postFix);
    }

    /**
     * @inheritdoc
     */
    public function snapshotImpressionsCount($adTagId, $postFix)
    {
        $impressions = $this->cacheEventCounter->getImpressionCount($adTagId);

        $this->saveNewKeyForAdTag($adTagId, CacheEventCounter::CACHE_KEY_IMPRESSION, $impressions, $postFix);
    }

    /**
     * @inheritdoc
     */
    public function snapshotVerifyImpressionsCount($adTagId, $postFix)
    {
        $verifyImpressions = $this->cacheEventCounter->getVerifiedImpressionCount($adTagId);

        $this->saveNewKeyForAdTag($adTagId, CacheEventCounter::CACHE_KEY_VERIFIED_IMPRESSION, $verifyImpressions, $postFix);
    }

    /**
     * @inheritdoc
     */
    public function snapshotPassbacksCount($adTagId, $postFix)
    {
        $passBacks = $this->cacheEventCounter->getPassbackCount($adTagId);

        $this->saveNewKeyForAdTag($adTagId, CacheEventCounter::CACHE_KEY_PASSBACK, $passBacks, $postFix);
    }

    /**
     * @inheritdoc
     */
    public function snapshotUnVerifyImpressionsCount($adTagId, $postFix)
    {
        $unVerifyImpressions = $this->cacheEventCounter->getUnverifiedImpressionCount($adTagId);

        $this->saveNewKeyForAdTag($adTagId, CacheEventCounter::CACHE_KEY_UNVERIFIED_IMPRESSION, $unVerifyImpressions, $postFix);
    }

    /**
     * @inheritdoc
     */
    public function snapshotFirstOpportunitiesCount($adTagId, $postFix)
    {
        $firstOpportunities = $this->cacheEventCounter->getFirstOpportunityCount($adTagId);

        $this->saveNewKeyForAdTag($adTagId, CacheEventCounter::CACHE_KEY_FIRST_OPPORTUNITY, $firstOpportunities, $postFix);
    }

    /**
     * @inheritdoc
     */
    public function snapshotSlotOpportunitiesCount($adSlotId, $postFix)
    {
        $adSlotOpportunities = $this->cacheEventCounter->getSlotOpportunityCount($adSlotId);

        $this->saveNewKeyForAdSlot($adSlotId, CacheEventCounter::CACHE_KEY_SLOT_OPPORTUNITY, $adSlotOpportunities, $postFix);
    }

    /**
     * @param $adTagId
     * @param $constant
     * @param $value
     * @param $postFix
     */
    private function saveNewKeyForAdTag($adTagId, $constant, $value, $postFix)
    {
        $namespace = $this->cacheEventCounter->getNamespace(CacheEventCounter::NAMESPACE_AD_TAG, $adTagId);
        $key = $this->cacheEventCounter->getCacheKey($constant, $namespace);

        //Build new key
        $snapShotKey = sprintf("%s%s", $key, $postFix);

        $this->saveToRedis($snapShotKey, $value);
    }

    /**
     * @param $adSlotId
     * @param $constant
     * @param $value
     * @param $postFix
     */
    private function saveNewKeyForAdSlot($adSlotId, $constant, $value, $postFix)
    {
        $namespace = $this->cacheEventCounter->getNamespace(CacheEventCounter::NAMESPACE_AD_SLOT, $adSlotId);
        $key = $this->cacheEventCounter->getCacheKey($constant, $namespace);

        //Build new key
        $snapShotKey = sprintf("%s%s", $key, $postFix);

        $this->saveToRedis($snapShotKey, $value);
    }

    /**
     * @param $key
     * @param $value
     */
    private function saveToRedis($key, $value)
    {
        $cache = $this->cacheEventCounter->getCache();
        if ($cache instanceof RedisCache || $cache instanceof \Tagcade\Cache\Legacy\Cache\RedisCache) {
            $redis = $cache->getRedis();
            $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
            $redis->set($key, $value, $this->cacheKeyTimeOut);
        }
    }
}