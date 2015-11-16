<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Counter;

use DateTime;
use Doctrine\Common\Cache\Cache;
use Tagcade\Cache\Legacy\Cache\RedisArrayCacheInterface;
use Tagcade\Domain\DTO\Report\Performance\AdSlotReportCount;
use Tagcade\Domain\DTO\Report\Performance\AdTagReportCount;
use Tagcade\Domain\DTO\Report\Performance\RonAdSlotReportCount;
use Tagcade\Domain\DTO\Report\Performance\RonAdTagReportCount;

class CacheEventCounter extends AbstractEventCounter implements CacheEventCounterInterface
{
    const KEY_DATE_FORMAT                  = 'ymd';

    const CACHE_KEY_FALLBACK               = 'fallbacks'; // legacy

    const CACHE_KEY_SLOT_OPPORTUNITY       = 'opportunities'; // same "opportunities" key, used with different namespace
    const CACHE_KEY_OPPORTUNITY            = 'opportunities';
    const CACHE_KEY_FIRST_OPPORTUNITY      = 'first_opportunities';
    const CACHE_KEY_IMPRESSION             = 'impressions';
    const CACHE_KEY_VERIFIED_IMPRESSION    = 'verified_impressions';
    const CACHE_KEY_UNVERIFIED_IMPRESSION  = 'unverified_impressions';
    const CACHE_KEY_BLANK_IMPRESSION       = 'blank_impressions';
    const CACHE_KEY_VOID_IMPRESSION        = 'void_impressions';
    const CACHE_KEY_CLICK                  = 'clicks';
    const CACHE_KEY_PASSBACK               = 'passbacks'; // legacy name is fallbacks
    const CACHE_KEY_FORCED_PASSBACK        = 'forced_passbacks'; // not counted yet for now

    const NAMESPACE_AD_SLOT                = 'adslot_%d';
    const NAMESPACE_AD_TAG                 = 'adtag_%d';


    const NAMESPACE_RON_AD_SLOT            = 'ron_slot_%d';
    const NAMESPACE_RON_AD_TAG             = 'ron_tag_%d';
    const NAMESPACE_APPEND_SEGMENT         = 'segment_%d';

    const REDIS_HASH_EVENT_COUNT           = 'event_processor:event_count';

    private static $AD_TAG_REPORT_KEYS = [
        0 => self::CACHE_KEY_OPPORTUNITY,
        1 => self::CACHE_KEY_IMPRESSION,
        2 => self::CACHE_KEY_FIRST_OPPORTUNITY,
        3 => self::CACHE_KEY_VERIFIED_IMPRESSION,
        4 => self::CACHE_KEY_PASSBACK,
        5 => self::CACHE_KEY_UNVERIFIED_IMPRESSION,
        6 => self::CACHE_KEY_BLANK_IMPRESSION,
        7 => self::CACHE_KEY_VOID_IMPRESSION,
        8 => self::CACHE_KEY_CLICK,
    ];

    /**
     * @var RedisArrayCacheInterface
     */
    protected $cache;

    protected $formattedDate;
    protected $useLocalCache = true;
    private $localCache = array();

    public function __construct(RedisArrayCacheInterface $cache)
    {
        $this->cache = $cache;
        $this->setDate(new DateTime('today'));
    }

    public function setDate(DateTime $date = null)
    {
        if (!$date) {
            $date = new DateTime('today');
        }

        $this->date = $date;
        $this->formattedDate = $date->format(self::KEY_DATE_FORMAT);
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function getSlotOpportunityCount($slotId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_SLOT, $slotId);

        return $this->fetchFromCache(
            $this->getCacheKey(static::CACHE_KEY_SLOT_OPPORTUNITY, $namespace)
        );
    }

    public function getOpportunityCount($tagId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $tagId);

        return $this->fetchFromCache(
            $this->getCacheKey(static::CACHE_KEY_OPPORTUNITY, $namespace)
        );
    }

    public function getImpressionCount($tagId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $tagId);

        return $this->fetchFromCache(
            $this->getCacheKey(static::CACHE_KEY_IMPRESSION, $namespace)
        );
    }

    public function getPassbackCount($tagId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $tagId);

        $legacyCount = (int)$this->fetchFromCache(
            $this->getCacheKey(static::CACHE_KEY_FALLBACK, $namespace)
        );

        $passbackCount = (int)$this->fetchFromCache(
            $this->getCacheKey(self::CACHE_KEY_PASSBACK, $namespace)
        );

        return ($legacyCount + $passbackCount);
    }

    public function getFirstOpportunityCount($tagId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $tagId);

        return $this->fetchFromCache(
          $this->getCacheKey(self::CACHE_KEY_FIRST_OPPORTUNITY, $namespace)
        );

    }

    public function getVerifiedImpressionCount($tagId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $tagId);

        return $this->fetchFromCache(
            $this->getCacheKey(self::CACHE_KEY_VERIFIED_IMPRESSION, $namespace)
        );
    }

    public function getUnverifiedImpressionCount($tagId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $tagId);

        return $this->fetchFromCache(
            $this->getCacheKey(self::CACHE_KEY_UNVERIFIED_IMPRESSION, $namespace)
        );
    }

    public function getBlankImpressionCount($tagId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $tagId);

        return $this->fetchFromCache(
            $this->getCacheKey(self::CACHE_KEY_BLANK_IMPRESSION, $namespace)
        );
    }

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getVoidImpressionCount($tagId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $tagId);

        return $this->fetchFromCache(
            $this->getCacheKey(self::CACHE_KEY_VOID_IMPRESSION, $namespace)
        );
    }

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getClickCount($tagId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $tagId);

        return $this->fetchFromCache(
            $this->getCacheKey(self::CACHE_KEY_CLICK, $namespace)
        );
    }


    public function useLocalCache($bool)
    {
        $this->useLocalCache = (bool) $bool;
    }

    public function resetLocalCache()
    {
        $this->localCache = array();
    }

    /**
     * @param int $ronSlotId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonSlotOpportunityCount($ronSlotId, $segment = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_RON_AD_SLOT, $ronSlotId, self::NAMESPACE_APPEND_SEGMENT, $segment);

        return $this->hFetchFromCache(self::REDIS_HASH_EVENT_COUNT, $this->getCacheKey(static::CACHE_KEY_SLOT_OPPORTUNITY, $namespace));
    }

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonOpportunityCount($ronTagId, $segment = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_RON_AD_TAG, $ronTagId, self::NAMESPACE_APPEND_SEGMENT, $segment);

        return $this->hFetchFromCache(self::REDIS_HASH_EVENT_COUNT, $this->getCacheKey(static::CACHE_KEY_OPPORTUNITY, $namespace));
    }

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonImpressionCount($ronTagId, $segment = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_RON_AD_TAG, $ronTagId, self::NAMESPACE_APPEND_SEGMENT, $segment);

        return $this->hFetchFromCache(self::REDIS_HASH_EVENT_COUNT, $this->getCacheKey(static::CACHE_KEY_IMPRESSION, $namespace));

    }

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonPassbackCount($ronTagId, $segment = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_RON_AD_TAG, $ronTagId, self::NAMESPACE_APPEND_SEGMENT, $segment);

        return $this->hFetchFromCache(self::REDIS_HASH_EVENT_COUNT, $this->getCacheKey(static::CACHE_KEY_PASSBACK, $namespace));
    }

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonFirstOpportunityCount($ronTagId, $segment = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_RON_AD_TAG, $ronTagId, self::NAMESPACE_APPEND_SEGMENT, $segment);

        return $this->hFetchFromCache(self::REDIS_HASH_EVENT_COUNT, $this->getCacheKey(static::CACHE_KEY_FIRST_OPPORTUNITY, $namespace));
    }

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonVerifiedImpressionCount($ronTagId, $segment = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_RON_AD_TAG, $ronTagId, self::NAMESPACE_APPEND_SEGMENT, $segment);

        return $this->hFetchFromCache(self::REDIS_HASH_EVENT_COUNT, $this->getCacheKey(static::CACHE_KEY_VERIFIED_IMPRESSION, $namespace));
    }

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonUnverifiedImpressionCount($ronTagId, $segment = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_RON_AD_TAG, $ronTagId, self::NAMESPACE_APPEND_SEGMENT, $segment);

        return $this->hFetchFromCache(self::REDIS_HASH_EVENT_COUNT, $this->getCacheKey(static::CACHE_KEY_UNVERIFIED_IMPRESSION, $namespace));
    }

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonBlankImpressionCount($ronTagId, $segment = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_RON_AD_TAG, $ronTagId, self::NAMESPACE_APPEND_SEGMENT, $segment);

        return $this->hFetchFromCache(self::REDIS_HASH_EVENT_COUNT, $this->getCacheKey(static::CACHE_KEY_BLANK_IMPRESSION, $namespace));
    }

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonVoidImpressionCount($ronTagId, $segment = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_RON_AD_TAG, $ronTagId, self::NAMESPACE_APPEND_SEGMENT, $segment);

        return $this->hFetchFromCache(self::REDIS_HASH_EVENT_COUNT, $this->getCacheKey(static::CACHE_KEY_VOID_IMPRESSION, $namespace));
    }

    /**
     * @param int $ronTagId
     * @param int|null $segment
     * @return mixed
     */
    public function getRonClickCount($ronTagId, $segment = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_RON_AD_TAG, $ronTagId, self::NAMESPACE_APPEND_SEGMENT, $segment);

        return $this->hFetchFromCache(self::REDIS_HASH_EVENT_COUNT, $this->getCacheKey(static::CACHE_KEY_CLICK, $namespace));

    }

    public function getAdSlotReports(array $adSlotIds)
    {
        $cacheKeys =[];
        $convertedResults = [];

        foreach ($adSlotIds as $id) {
            $namespace = $this->getNamespace(self::NAMESPACE_AD_SLOT, $id);
            $cacheKeys[] = $this->getCacheKey(self::CACHE_KEY_SLOT_OPPORTUNITY, $namespace);
        }

        $results = $this->cache->mGet($cacheKeys); // sequence of output is sequence of slot ids
        $index = 0;

        foreach($adSlotIds as $id) {
            $convertedResults[$id] = new AdSlotReportCount(array(self::CACHE_KEY_SLOT_OPPORTUNITY => $results[$index]));
            $index ++;
        }

        return $convertedResults;
    }


    public function getAdTagReports(array $tagIds, $nativeSlot = false)
    {
        $convertedResults =[];
        $adTagKeys = [];
        $tagKeyCount = 0;

        foreach ($tagIds as $id) {
            $cacheKeysForThisTag = $this->createCacheKeysForAdTag($id, $nativeSlot);
            if ($tagKeyCount === 0) {
                $tagKeyCount = count($cacheKeysForThisTag);
            }

            foreach ($cacheKeysForThisTag as $k) {
                $adTagKeys[] = $k;
            }
        }

        $results = $this->cache->mGet($adTagKeys);
        $totalResultCount = count($results);
        $index = 0;

        foreach($tagIds as $tagId) {
            if ($index + $tagKeyCount > $totalResultCount) {
                throw new \RuntimeException('something went wrong with redis fetching multiple keys');
            }

            $singleConvertedResults = [];

            $adTagReportKey = 0;
            for($i = $index; $i < $index + $tagKeyCount ; $i ++) {
                $singleConvertedResults[static::$AD_TAG_REPORT_KEYS[$adTagReportKey]] = $results[$i];
                $adTagReportKey ++;
            }

            $convertedResults[$tagId] = new AdTagReportCount($singleConvertedResults);
            $index += $tagKeyCount;
        }

        return $convertedResults;
    }


    /**
     * @inheritdoc
     */
    public function getAdTagReport($tagId, $nativeSlot = false)
    {
        $adTagKeys = $this->createCacheKeysForAdTag($tagId, $nativeSlot);
        $results = $this->cache->mGet($adTagKeys);
        $convertedResults = array();
        foreach ($results as $index => $value) {
            $convertedResults[static::$AD_TAG_REPORT_KEYS[$index]] = $value;
        }

        return new AdTagReportCount($convertedResults);
    }

    public function getRonAdTagReports(array $tagIds, $segmentId = null, $nativeSlot = false)
    {
        $ronTagKeys = [];
        foreach ($tagIds as $id) {
            $tmpCacheKeys = $this->createCacheKeysForRonTag($id, $segmentId, $nativeSlot);
            foreach($tmpCacheKeys as $key) {
                $ronTagKeys[] = $key; // note: should not use array_merge to reduce overhead of function call
            }
        }

        $results = $this->cache->hMGet(self::REDIS_HASH_EVENT_COUNT, $ronTagKeys);
        $reports = [];
        foreach($tagIds as $id) {
            $reports[] = new RonAdTagReportCount($id, $results, $segmentId);
        }

        return $reports;
    }

    public function getRonAdTagReport($ronTagId, $segmentId = null, $hasNativeSlotContainer = false)
    {
        $ronTagKeys = $this->createCacheKeysForRonTag($ronTagId, $segmentId, $hasNativeSlotContainer);

        $results = $this->cache->hMGet(self::REDIS_HASH_EVENT_COUNT, $ronTagKeys);

        return new RonAdTagReportCount($ronTagId, $results, $segmentId);
    }

    public function getRonAdSlotReport($ronAdSlotId, $segmentId = null)
    {
        $ronAdSlotKeys = $this->createCacheKeysForRonAdSlot($ronAdSlotId, $segmentId);

        $results = $this->cache->hMGet(self::REDIS_HASH_EVENT_COUNT, $ronAdSlotKeys);

        return new RonAdSlotReportCount($ronAdSlotId, $results, $segmentId);
    }


    protected function createCacheKeysForAdTag($tagId, $hasNativeSlotContainer = false)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $tagId);

        return $this->createCacheKeysForTag($namespace, $hasNativeSlotContainer);
    }

    protected function createCacheKeysForRonTag($ronTagId, $segment = null, $hasNativeRonSlotContainer = false)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_RON_AD_TAG, $ronTagId, self::NAMESPACE_APPEND_SEGMENT, $segment);

        return $this->createCacheKeysForTag($namespace, $hasNativeRonSlotContainer);
    }

    protected function createCacheKeysForRonAdSlot($ronSlotId, $segment = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_RON_AD_SLOT, $ronSlotId, self::NAMESPACE_APPEND_SEGMENT, $segment);
        $cacheKeys[] = $this->getCacheKey(self::CACHE_KEY_SLOT_OPPORTUNITY, $namespace);

        return $cacheKeys;
    }

    protected function createCacheKeysForTag($namespace, $hasNativeSlotContainer = false)
    {
        $adTagKeys = array(
            $this->getCacheKey(self::CACHE_KEY_OPPORTUNITY, $namespace),
            $this->getCacheKey(self::CACHE_KEY_IMPRESSION, $namespace),
        );

        if (false === $hasNativeSlotContainer) {
            $adTagKeys[] = $this->getCacheKey(self::CACHE_KEY_FIRST_OPPORTUNITY, $namespace);
            $adTagKeys[] = $this->getCacheKey(self::CACHE_KEY_VERIFIED_IMPRESSION, $namespace);
            $adTagKeys[] = $this->getCacheKey(self::CACHE_KEY_PASSBACK, $namespace);
            $adTagKeys[] = $this->getCacheKey(self::CACHE_KEY_UNVERIFIED_IMPRESSION, $namespace);
            $adTagKeys[] = $this->getCacheKey(self::CACHE_KEY_BLANK_IMPRESSION, $namespace);
            $adTagKeys[] = $this->getCacheKey(self::CACHE_KEY_VOID_IMPRESSION, $namespace);
            $adTagKeys[] = $this->getCacheKey(self::CACHE_KEY_CLICK, $namespace);
        }

        return $adTagKeys;
    }
    /**
     * @param string $key
     * @return mixed
     */
    protected function fetchFromCache($key)
    {
        if ($this->useLocalCache && array_key_exists($key, $this->localCache)) {
            return $this->localCache[$key];
        }

        $value = $this->cache->fetch($key);

        if ($this->useLocalCache && $value !== false) {
            $this->localCache[$key] = $value;
        }

        return $value;
    }


    /**
     * @param $hash
     * @param $field
     * @return mixed
     */
    protected function hFetchFromCache($hash, $field)
    {
        $localCacheKey = "$hash:$field";
        if ($this->useLocalCache && array_key_exists($localCacheKey, $this->localCache)) {
            return $this->localCache[$localCacheKey];
        }

        $value = $this->cache->hFetch($hash, $field);

        if ($this->useLocalCache && $value !== false) {
            $this->localCache[$localCacheKey] = $value;
        }

        return $value;
    }

    /**
     * Does append namespace if id != null. Otherwise returning original namespace
     * @param $namespace
     * @param string|null $appendFormat
     * @param int|null $id
     * @return string
     */
    private function appendNamespace($namespace, $appendFormat = null, $id = null)
    {
        return (null !== $id && null !== $appendFormat) ? sprintf($namespace . ':' . $appendFormat, $id) : $namespace;
    }

    public function getNamespace($namespaceFormat, $id, $appendingFormat = null, $appendingId = null)
    {
        $namespace = sprintf($namespaceFormat, $id);

        return $this->appendNamespace($namespace, $appendingFormat, $appendingId);
    }

//    public function getNamespace($namespace, $id)
//    {
//        return sprintf($namespace, $id);
//    }

    public function getCacheKey($key, $namespace)
    {
        $keyFormat = '%s:%s:%s';
        return sprintf($keyFormat, $key, $namespace, $this->formattedDate);
    }
}