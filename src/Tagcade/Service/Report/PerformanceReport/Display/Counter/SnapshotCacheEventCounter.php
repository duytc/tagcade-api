<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Counter;

use Redis;
use Tagcade\Cache\RedisCache;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;

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
    public function snapshotAdTagInbannerImpressions($adTagId, $adSlotId, $postFix)
    {
        $inBannerImpression = $this->cacheEventCounter->getAdTagInBannerImpressionCount($adSlotId, $adTagId);

        if ($inBannerImpression) {
            $this->hSaveNewKeyForAdTag($adTagId, $adSlotId, CacheEventCounter::CACHE_KEY_IN_BANNER_IMPRESSION, $inBannerImpression, $postFix);
        }
    }

    /**
     * @inheritdoc
     */
    public function snapshotAdTagInbannerRequest($adTagId, $adSlotId, $postFix)
    {
        $inBannerRequest = $this->cacheEventCounter->getAdTagInBannerRequestCount($adSlotId, $adTagId);

        if ($inBannerRequest) {
            $this->hSaveNewKeyForAdTag($adTagId, $adSlotId, CacheEventCounter::CACHE_KEY_IN_BANNER_REQUEST, $inBannerRequest, $postFix);
        }
    }

    /**
     * @inheritdoc
     */
    public function snapshotAdTagInbannerTimeOut($adTagId, $adSlotId, $postFix)
    {
        $inBannerTimeOut = $this->cacheEventCounter->getAdTagInBannerTimeoutCount($adSlotId, $adTagId);

        if ($inBannerTimeOut){
            $this->hSaveNewKeyForAdTag($adTagId, $adSlotId, CacheEventCounter::CACHE_KEY_IN_BANNER_TIMEOUT, $inBannerTimeOut, $postFix);
        }
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
     * @inheritdoc
     */
    public function snapshotAdSlot(ReportableAdSlotInterface $adSlot, $postFix)
    {
        $adSlotReport = $this->cacheEventCounter->getAdSlotReport($adSlot);
        $adSlotId = $adSlot->getId();
        // notice: CacheEventCounter::CACHE_KEY_XXX for cache key
        // SnapshotCreatorInterface::CACHE_KEY_XXX for data key of snapshot, not cache key

        //1) "opportunities:adslot_478:180507"
        $this->saveNewKeyForAdSlot($adSlotId, CacheEventCounter::CACHE_KEY_SLOT_OPPORTUNITY, $adSlotReport[SnapshotCreatorInterface::CACHE_KEY_SLOT_OPPORTUNITY], $postFix);

        //2) "refreshes:adslot_478:180507"
        $this->saveNewKeyForAdSlot($adSlotId, CacheEventCounter::CACHE_KEY_SLOT_OPPORTUNITY_REFRESHES, $adSlotReport[SnapshotCreatorInterface::CACHE_KEY_SLOT_OPPORTUNITY_REFRESHES], $postFix);

        //3) "hb bid request:adslot_478:180507"
        $this->saveNewKeyForAdSlot($adSlotId, CacheEventCounter::CACHE_KEY_HB_BID_REQUEST, $adSlotReport[SnapshotCreatorInterface::CACHE_KEY_HEADER_BID_REQUEST], $postFix);

        //4) "inbanner impression:adslot_478:180507"
        $this->hSaveNewKeyForAdSlot($adSlotId, CacheEventCounter::CACHE_KEY_IN_BANNER_IMPRESSION , $adSlotReport[SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_IMPRESSION], $postFix);

        //5) "inbanner request:adslot_478:180507"
        $this->hSaveNewKeyForAdSlot($adSlotId, CacheEventCounter::CACHE_KEY_IN_BANNER_REQUEST, $adSlotReport[SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_REQUEST], $postFix);

        //6) "inbanner time out:adslot_478:180507"
        $this->hSaveNewKeyForAdSlot($adSlotId, CacheEventCounter::CACHE_KEY_IN_BANNER_TIMEOUT, $adSlotReport[SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_TIMEOUT], $postFix);
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

    /**
     * @param $adSlotId
     * @param $constant
     * @param $value
     * @param $postFix
     */
    private function hSaveNewKeyForAdSlot($adSlotId, $constant, $value, $postFix)
    {
        $namespace = $this->cacheEventCounter->getNamespace(CacheEventCounter::NAMESPACE_AD_SLOT, $adSlotId);
        $cacheKey = $this->cacheEventCounter->getCacheKey($constant, $namespace);

        //Build new key
        $snapShotCacheKey = sprintf("%s%s", $cacheKey, $postFix);

        $this->hSaveToRedis($snapShotCacheKey, $value);
    }

    /**
     * @param $key
     * @param $value
     */
    private function hSaveToRedis($key, $value)
    {
        $cache = $this->cacheEventCounter->getCache();
        if ($cache instanceof RedisCache || $cache instanceof \Tagcade\Cache\Legacy\Cache\RedisCache) {
            $redis = $cache->getRedis();
            $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
            $cache->hSave(
                $hash = $this->getHashFieldDate(CacheEventCounter::REDIS_HASH_IN_BANNER_EVENT_COUNT),
                $key,
                $value
            );
        }
    }

    /**
     * @param $adTagId
     * @param $adSlotId
     * @param $constant
     * @param $value
     * @param $postFix
     */
    private function hSaveNewKeyForAdTag($adTagId, $adSlotId, $constant, $value, $postFix)
    {
        $namespace = $this->cacheEventCounter->getNamespace(CacheEventCounter::NAMESPACE_AD_SLOT, $adSlotId, CacheEventCounter::NAMESPACE_AD_TAG, $adTagId);
        $cacheKey = $this->cacheEventCounter->getCacheKey($constant, $namespace);

        //Build new key
        $snapShotKey = sprintf("%s%s", $cacheKey, $postFix);

        $this->hSaveToRedis($snapShotKey, $value);
    }

    /**
     * @param $hashField
     * @return mixed
     */
    private function getHashFieldDate($hashField)
    {
        $date = $this->cacheEventCounter->getDate()->format('ymd');
        //Build new hash field
        return sprintf("%s:%s", $hashField, $date);
    }

    public function setExpiredTimeForHashFieldDate()
    {
        $cache = $this->cacheEventCounter->getCache();
        if ($cache instanceof RedisCache || $cache instanceof \Tagcade\Cache\Legacy\Cache\RedisCache) {
            $redis = $cache->getRedis();
            $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);

            $cache->expire($this->getHashFieldDate(CacheEventCounter::REDIS_HASH_IN_BANNER_EVENT_COUNT), $this->cacheKeyTimeOut);
        }
    }
}