<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Counter;

use Doctrine\Common\Cache\Cache;
use Tagcade\Exception\InvalidArgumentException;

class CacheEventCounter extends AbstractEventCounter implements CacheEventCounterInterface
{
    /**
     * This is ported from legacy code
     */

    const SLOT_OPPORTUNITY = 0;
    const OPPORTUNITY      = 1;
    const IMPRESSION       = 2;
    const FALLBACK         = 3; // means the same as passback or default

    const KEY_DATE_FORMAT          = 'ymd';

    const CACHE_KEY_OPPORTUNITY    = 'opportunities';
    const CACHE_KEY_IMPRESSION     = 'impressions';
    const CACHE_KEY_FALLBACK       = 'fallbacks';

    // longnh2:
    const CACHE_KEY_SLOT_OPPORTUNITY       = 'opportunities'; // same "opportunities" key, used with different namespace
    const CACHE_KEY_FIRST_OPPORTUNITY      = 'first_opportunities';
    const CACHE_KEY_VERIFIED_IMPRESSION    = 'verified_impressions';
    const CACHE_KEY_UNVERIFIED_IMPRESSION  = 'unverified_impressions';
    const CACHE_KEY_BLANK_IMPRESSION       = 'blank_impressions';
    const CACHE_KEY_PASSBACK       = 'passbacks'; // legacy name is fallbacks
    const CACHE_KEY_FORCED_PASSBACK        = 'forced_passbacks'; // not counted yet for now


    const NAMESPACE_AD_SLOT                = 'adslot_%d';
    const NAMESPACE_AD_TAG                 = 'adtag_%d';
    /**
     * @var Cache
     */
    protected $cache;

    protected $useLocalCache = true;
    private $localCache = array();

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
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
            $this->getCacheKey(static::OPPORTUNITY, $namespace)
        );
    }

    public function getImpressionCount($tagId)
    {
        return $this->fetchFromCache(
            $this->getCacheKey(static::IMPRESSION, $tagId)
        );
    }

    public function getPassbackCount($tagId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $tagId);

        $legacyCount = (int)$this->fetchFromCache(
            $this->getCacheKey(static::FALLBACK, $namespace)
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

    public function useLocalCache($bool)
    {
        $this->useLocalCache = (bool) $bool;
    }

    public function resetLocalCache()
    {
        $this->localCache = array();
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

    private function getNamespace($namespace, $id)
    {
        return sprintf($namespace, $id);
    }

    public function getCacheKey($key, $namespace)
    {
        $keyFormat = '%s:%s:%s';
        return sprintf($keyFormat, $key, $namespace, $this->date->format(self::KEY_DATE_FORMAT));
    }
}