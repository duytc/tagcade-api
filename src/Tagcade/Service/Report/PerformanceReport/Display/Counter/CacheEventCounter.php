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

    const CACHE_KEY_AD_SLOT_FORMAT = 'adslot_%d';
    const CACHE_KEY_AD_TAG_FORMAT  = 'adtag_%d';

    /**
     * @var Cache
     */
    protected $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
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
    public function getSlotOpportunityCount($slotId)
    {
        return $this->cache->fetch(
            $this->getCacheKey(static::SLOT_OPPORTUNITY, $slotId)
        );
    }

    /**
     * @inheritdoc
     */
    public function getOpportunityCount($tagId)
    {
        return $this->cache->fetch(
            $this->getCacheKey(static::OPPORTUNITY, $tagId)
        );
    }

    /**
     * @inheritdoc
     */
    public function getImpressionCount($tagId)
    {
        return $this->cache->fetch(
            $this->getCacheKey(static::IMPRESSION, $tagId)
        );
    }

    /**
     * @inheritdoc
     */
    public function getPassbackCount($tagId)
    {
        return $this->cache->fetch(
            $this->getCacheKey(static::FALLBACK, $tagId)
        );
    }

    public function getCacheKey($type, $id)
    {
        $keyFormat = '%s:%s:%s';

        switch($type) {
            case static::SLOT_OPPORTUNITY:
                $bucket = self::CACHE_KEY_OPPORTUNITY;
                $entity = self::CACHE_KEY_AD_SLOT_FORMAT;
                break;
            case static::OPPORTUNITY:
                $bucket = self::CACHE_KEY_OPPORTUNITY;
                $entity = self::CACHE_KEY_AD_TAG_FORMAT;
                break;
            case static::IMPRESSION:
                $bucket = self::CACHE_KEY_IMPRESSION;
                $entity = self::CACHE_KEY_AD_TAG_FORMAT;
                break;
            case static::FALLBACK:
                $bucket = self::CACHE_KEY_FALLBACK;
                $entity = self::CACHE_KEY_AD_TAG_FORMAT;
                break;
            default:
                throw new InvalidArgumentException('invalid event counter cache type');
        }

        $entity = sprintf($entity, $id);

        return sprintf($keyFormat, $bucket, $entity, $this->getDate()->format(self::KEY_DATE_FORMAT));
    }
}