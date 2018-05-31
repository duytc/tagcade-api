<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Counter;

use DateTime;
use Redis;
use Tagcade\Cache\RedisCacheInterface;
use Tagcade\Domain\DTO\Report\Performance\AccountReportCount;
use Tagcade\Domain\DTO\Report\Performance\AdSlotReportCount;
use Tagcade\Domain\DTO\Report\Performance\AdTagReportCount;
use Tagcade\Domain\DTO\Report\Performance\RonAdSlotReportCount;
use Tagcade\Domain\DTO\Report\Performance\RonAdTagReportCount;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;

class CacheEventCounter extends AbstractEventCounter implements CacheEventCounterInterface
{
    const KEY_DATE_FORMAT                  = 'ymd';
    const KEY_DATE_HOUR_FORMAT             = 'ymdH';

    const CACHE_KEY_ACC_SLOT_OPPORTUNITY   = 'slot_opportunities';
    const CACHE_KEY_ACC_OPPORTUNITY        = 'opportunities';
    const CACHE_KEY_SLOT_OPPORTUNITY       = 'opportunities'; // same "opportunities" key, used with different namespace
    const CACHE_KEY_SLOT_OPPORTUNITY_REFRESHES = 'refreshes'; // same "refreshes" key, used with different namespace
    const CACHE_KEY_OPPORTUNITY            = 'opportunities';
    const CACHE_KEY_FIRST_OPPORTUNITY      = 'first_opportunities';
    const CACHE_KEY_IMPRESSION             = 'impressions';
    const CACHE_KEY_VERIFIED_IMPRESSION    = 'verified_impressions';
    const CACHE_KEY_UNVERIFIED_IMPRESSION  = 'unverified_impressions';
    const CACHE_KEY_BLANK_IMPRESSION       = 'blank_impressions';
    const CACHE_KEY_VOID_IMPRESSION        = 'void_impressions';
    const CACHE_KEY_CLICK                  = 'clicks';
    const CACHE_KEY_REFRESHES              = 'refreshes';
    const CACHE_KEY_FALLBACK               = 'fallbacks'; // legacy
    const CACHE_KEY_PASSBACK               = 'passbacks'; // legacy name is fallbacks
    const CACHE_KEY_FORCED_PASSBACK        = 'forced_passbacks'; // not counted yet for now
    const CACHE_KEY_HB_BID_REQUEST         = 'hb_bid_request';
    const CACHE_KEY_IN_BANNER_REQUEST      = 'requests';
    const CACHE_KEY_IN_BANNER_IMPRESSION   = 'impressions';
    const CACHE_KEY_IN_BANNER_TIMEOUT      = 'timeouts';

    const KEY_OPPORTUNITY                  = 'opportunities';
    const KEY_SLOT_OPPORTUNITY             = 'slot_opportunities';
    const KEY_IMPRESSIONS                  = 'impressions';
    const KEY_HB_REQUESTS                  = 'hb_requests';
    const KEY_PASSBACKS                    = 'passbacks';
    const KEY_FALLBACKS                    = 'fallbacks';
    const KEY_IN_BANNER_IMPRESSIONS        = 'inbanner_impressions';
    const KEY_IN_BANNER_REQUESTS           = 'inbanner_requests';
    const KEY_IN_BANNER_TIMEOUTS           = 'inbanner_timeouts';

    const NAMESPACE_AD_SLOT                = 'adslot_%d';
    const NAMESPACE_AD_TAG                 = 'adtag_%d';
    const NAMESPACE_RON_AD_SLOT            = 'ron_slot_%d';
    const NAMESPACE_RON_AD_TAG             = 'ron_tag_%d';
    const NAMESPACE_APPEND_SEGMENT         = 'segment_%d';
    const NAMESPACE_ACCOUNT                = 'account_%d';

    const REDIS_HASH_EVENT_COUNT           = 'event_processor:event_count';
    const REDIS_HASH_IN_BANNER_EVENT_COUNT = 'inbanner_event_processor:event_count';

    private static $adTagReportKeys = [
        self::CACHE_KEY_OPPORTUNITY,
        self::CACHE_KEY_IMPRESSION,
        self::CACHE_KEY_FIRST_OPPORTUNITY,
        self::CACHE_KEY_VERIFIED_IMPRESSION,
        self::CACHE_KEY_PASSBACK,
        self::CACHE_KEY_UNVERIFIED_IMPRESSION,
        self::CACHE_KEY_BLANK_IMPRESSION,
        self::CACHE_KEY_VOID_IMPRESSION,
        self::CACHE_KEY_CLICK,
        self::CACHE_KEY_FALLBACK,
        self::CACHE_KEY_REFRESHES,
    ];

    private static $accountReportKeys = [
        self::CACHE_KEY_ACC_OPPORTUNITY,
        self::CACHE_KEY_ACC_SLOT_OPPORTUNITY,
        self::CACHE_KEY_IMPRESSION,
        self::CACHE_KEY_PASSBACK,
        self::CACHE_KEY_FALLBACK, // legacy key of CACHE_KEY_PASSBACK
    ];

    /**
     * @var RedisCacheInterface
     */
    protected $cache;

    protected $formattedDate;
    protected $useLocalCache = true;
    private $localCache = array();

    /**
     * @var AdTagManagerInterface
     */
    protected $adTagManager;

    /**
     * @var AdSlotManagerInterface
     */
    protected $adSlotManager;

    protected $pipelineSizeThreshold;
    public function __construct(RedisCacheInterface $cache, AdTagManagerInterface $adTagManager, AdSlotManagerInterface $adSlotManager, $pipelineSizeThreshold)
    {
        $this->adTagManager = $adTagManager;
        $this->adSlotManager = $adSlotManager;
        $this->cache = $cache;
        $this->setDate(new DateTime('today'));
        $this->pipelineSizeThreshold = $pipelineSizeThreshold;
    }

    public function setDate(DateTime $date = null)
    {
        if (!$date) {
            $date = new DateTime('today');
        }

        $this->date = $date;
        $this->formattedDate = !$this->getDataWithDateHour() ? $date->format(self::KEY_DATE_FORMAT) : $date->format(self::KEY_DATE_HOUR_FORMAT);
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

    public function getSlotOpportunityRefreshesCount($slotId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_SLOT, $slotId);

        return $this->fetchFromCache(
            $this->getCacheKey(static::CACHE_KEY_SLOT_OPPORTUNITY_REFRESHES, $namespace)
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

    public function getHeaderBidRequestCount($slotId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_SLOT, $slotId);

        return $this->fetchFromCache(
            $this->getCacheKey(static::CACHE_KEY_HB_BID_REQUEST, $namespace)
        );
    }

    public function getInBannerRequestCount($slotId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_SLOT, $slotId);
        $hash = !$this->getDataWithDateHour() ? self::REDIS_HASH_IN_BANNER_EVENT_COUNT : $this->getHashFieldDate();

        return $this->hFetchFromCache($hash,  $this->getCacheKey(static::CACHE_KEY_IN_BANNER_REQUEST, $namespace));
    }

    public function getAccountInBannerRequestCount($publisherId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_ACCOUNT, $publisherId);
        $hash = !$this->getDataWithDateHour() ? self::REDIS_HASH_IN_BANNER_EVENT_COUNT : $this->getHashFieldDate();

        return $this->hFetchFromCache($hash,  $this->getCacheKey(static::CACHE_KEY_IN_BANNER_REQUEST, $namespace));
    }

    public function getAdTagInBannerRequestCount($slotId, $tagId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_SLOT, $slotId, self::NAMESPACE_AD_TAG, $tagId);
        $hash = !$this->getDataWithDateHour() ? self::REDIS_HASH_IN_BANNER_EVENT_COUNT : $this->getHashFieldDate();

        return $this->hFetchFromCache($hash,  $this->getCacheKey(static::CACHE_KEY_IN_BANNER_REQUEST, $namespace));
    }


    public function getRonInBannerRequestCount($slotId, $segment = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_RON_AD_SLOT, $slotId, self::NAMESPACE_APPEND_SEGMENT, $segment);

        return $this->hFetchFromCache(self::REDIS_HASH_IN_BANNER_EVENT_COUNT,  $this->getCacheKey(static::CACHE_KEY_IN_BANNER_REQUEST, $namespace));
    }


    public function getInBannerImpressionCount($slotId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_SLOT, $slotId);
        $hash = !$this->getDataWithDateHour() ? self::REDIS_HASH_IN_BANNER_EVENT_COUNT : $this->getHashFieldDate();

        return $this->hFetchFromCache($hash,  $this->getCacheKey(static::CACHE_KEY_IN_BANNER_IMPRESSION, $namespace));
    }

    public function getAccountInBannerImpressionCount($publisherId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_ACCOUNT, $publisherId);
        $hash = !$this->getDataWithDateHour() ? self::REDIS_HASH_IN_BANNER_EVENT_COUNT : $this->getHashFieldDate();

        return $this->hFetchFromCache($hash, $this->getCacheKey(static::CACHE_KEY_IN_BANNER_IMPRESSION, $namespace));
    }

    public function getAdTagInBannerImpressionCount($slotId, $tagId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_SLOT, $slotId, self::NAMESPACE_AD_TAG, $tagId);
        $hash = !$this->getDataWithDateHour() ? self::REDIS_HASH_IN_BANNER_EVENT_COUNT : $this->getHashFieldDate();

        return $this->hFetchFromCache($hash, $this->getCacheKey(static::CACHE_KEY_IN_BANNER_IMPRESSION, $namespace));
    }


    public function getRonInBannerTimeoutCount($slotId, $segment = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_RON_AD_SLOT, $slotId, self::NAMESPACE_APPEND_SEGMENT, $segment);

        return $this->hFetchFromCache(self::REDIS_HASH_IN_BANNER_EVENT_COUNT,  $this->getCacheKey(static::CACHE_KEY_IN_BANNER_IMPRESSION, $namespace));
    }


    public function getInBannerTimeoutCount($slotId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_SLOT, $slotId);
        $hash = !$this->getDataWithDateHour() ? self::REDIS_HASH_IN_BANNER_EVENT_COUNT : $this->getHashFieldDate();

        return $this->hFetchFromCache($hash, $this->getCacheKey(static::CACHE_KEY_IN_BANNER_TIMEOUT, $namespace));
    }

    public function getAccountInBannerTimeoutCount($publisherId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_ACCOUNT, $publisherId);
        $hash = !$this->getDataWithDateHour() ? self::REDIS_HASH_IN_BANNER_EVENT_COUNT : $this->getHashFieldDate();

        return $this->hFetchFromCache($hash, $this->getCacheKey(static::CACHE_KEY_IN_BANNER_TIMEOUT, $namespace));
    }

    public function getAdTagInBannerTimeoutCount($slotId, $tagId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_SLOT, $slotId, self::NAMESPACE_AD_TAG, $tagId);
        $hash = !$this->getDataWithDateHour() ? self::REDIS_HASH_IN_BANNER_EVENT_COUNT : $this->getHashFieldDate();

        return $this->hFetchFromCache($hash, $this->getCacheKey(static::CACHE_KEY_IN_BANNER_TIMEOUT, $namespace));
    }


    public function getRonInBannerImpressionCount($slotId, $segment = null)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_RON_AD_SLOT, $slotId, self::NAMESPACE_APPEND_SEGMENT, $segment);

        return $this->hFetchFromCache(self::REDIS_HASH_IN_BANNER_EVENT_COUNT,  $this->getCacheKey(static::CACHE_KEY_IN_BANNER_TIMEOUT, $namespace));
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

    /**
     * @param int $tagId
     * @return int|bool
     */
    public function getRefreshesCount($tagId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $tagId);

        return $this->fetchFromCache(
            $this->getCacheKey(self::CACHE_KEY_REFRESHES, $namespace)
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

        $keys = array (
            $this->getCacheKey(static::CACHE_KEY_PASSBACK, $namespace),
            $this->getCacheKey(static::CACHE_KEY_FALLBACK, $namespace)
        );
        $result = $this->cache->hMGet(self::REDIS_HASH_EVENT_COUNT, $keys);

        return array_sum($result);
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

    public function getAdSlotReport(ReportableAdSlotInterface $slot)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_AD_SLOT, $slot->getId());
        $cacheKeys = array (
            $this->getCacheKey(self::CACHE_KEY_SLOT_OPPORTUNITY, $namespace),
            $this->getCacheKey(self::CACHE_KEY_SLOT_OPPORTUNITY_REFRESHES, $namespace),
            $this->getCacheKey(self::CACHE_KEY_HB_BID_REQUEST, $namespace)
        );

        $inBannerCacheKeys = array (
            $this->getCacheKey(self::CACHE_KEY_IN_BANNER_IMPRESSION, $namespace),
            $this->getCacheKey(self::CACHE_KEY_IN_BANNER_REQUEST, $namespace),
            $this->getCacheKey(self::CACHE_KEY_IN_BANNER_TIMEOUT, $namespace)
        );

        $results = $this->cache->mGet($cacheKeys);
        $hash = !$this->getDataWithDateHour() ? self::REDIS_HASH_IN_BANNER_EVENT_COUNT : $this->getHashFieldDate();
        $inBannerResults = $this->cache->hMGet($hash, $inBannerCacheKeys);

        $adTagCacheKeys = $this->getAdTagCacheKeysForAdSlot($slot);

        $pipe = $this->cache->multi(Redis::PIPELINE);
        array_walk($adTagCacheKeys, function($keys) use ($pipe) {
            $pipe->mGet($keys);
        });
        $adTagResults = $pipe->exec();

        return array (
            SnapshotCreatorInterface::CACHE_KEY_SLOT_OPPORTUNITY => $results[0],
            SnapshotCreatorInterface::CACHE_KEY_SLOT_OPPORTUNITY_REFRESHES => $results[1],
            SnapshotCreatorInterface::CACHE_KEY_HEADER_BID_REQUEST => $results[2],
            SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_IMPRESSION => $inBannerResults[$inBannerCacheKeys[0]],
            SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_REQUEST => $inBannerResults[$inBannerCacheKeys[1]],
            SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_TIMEOUT => $inBannerResults[$inBannerCacheKeys[2]],
            SnapshotCreatorInterface::CACHE_KEY_OPPORTUNITY => array_sum(array_column($adTagResults, 0)),
            SnapshotCreatorInterface::CACHE_KEY_IMPRESSION => array_sum(array_column($adTagResults, 1)),
            SnapshotCreatorInterface::CACHE_KEY_PASSBACK => array_sum(array_column($adTagResults, 2)) + array_sum(array_column($adTagResults, 3)),
//            SnapshotCreatorInterface::F => array_sum(array_column($adTagResults, 3))
        );
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
            $hbRequests = $this->getHeaderBidRequestCount($id);
            $inBannerRequests = $this->getInBannerRequestCount($id);
            $inBannerImpressions = $this->getInBannerImpressionCount($id);
            $inBannerTimeouts = $this->getInBannerTimeoutCount($id);
            $convertedResults[$id] = new AdSlotReportCount(
                array(
                    self::CACHE_KEY_SLOT_OPPORTUNITY => $results[$index],
                    self::CACHE_KEY_HB_BID_REQUEST => $hbRequests,
                    self::CACHE_KEY_IN_BANNER_REQUEST => $inBannerRequests,
                    self::CACHE_KEY_IN_BANNER_IMPRESSION => $inBannerImpressions,
                    self::CACHE_KEY_IN_BANNER_TIMEOUT => $inBannerTimeouts
                )
            );
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
                $singleConvertedResults[static::$adTagReportKeys[$adTagReportKey]] = $results[$i];
                $adTagReportKey ++;
            }

            $convertedResults[$tagId] = new AdTagReportCount($singleConvertedResults);
            $index += $tagKeyCount;
        }

        return $convertedResults;
    }

    public function getNetworkReport(array $tagIds, $nativeSlot = false)
    {
        $tempData = array (
            SnapshotCreatorInterface::CACHE_KEY_OPPORTUNITY => 0,
            SnapshotCreatorInterface::CACHE_KEY_IMPRESSION => 0,
            SnapshotCreatorInterface::CACHE_KEY_FIRST_OPPORTUNITY => 0,
            SnapshotCreatorInterface::CACHE_KEY_VERIFIED_IMPRESSION => 0,
            SnapshotCreatorInterface::CACHE_KEY_PASSBACK => 0,
            SnapshotCreatorInterface::CACHE_KEY_UNVERIFIED_IMPRESSION => 0,
            SnapshotCreatorInterface::CACHE_KEY_BLANK_IMPRESSION => 0,
            SnapshotCreatorInterface::CACHE_KEY_VOID_IMPRESSION => 0,
            SnapshotCreatorInterface::CACHE_KEY_CLICK => 0,
        );

        if (count($tagIds) < 1) {
            return $tempData;
        }

        $cacheKeys = [];
        $keyCount = 0;
        foreach ($tagIds as $id) {
            $cacheKeys[] = $this->createCacheKeysForAdTag($id, $nativeSlot);
            $keyCount++;
            if ($keyCount >= $this->pipelineSizeThreshold) {
                $pipe = $this->cache->multi(Redis::PIPELINE);
                array_walk($cacheKeys, function($keys) use ($pipe) {
                    $pipe->mGet($keys);
                });
                $results = $pipe->exec(); // sequence of output is sequence of slot ids
                $tempData[SnapshotCreatorInterface::CACHE_KEY_OPPORTUNITY] += array_sum(array_column($results, 0));
                $tempData[SnapshotCreatorInterface::CACHE_KEY_IMPRESSION] += array_sum(array_column($results, 1));

                if (false === $nativeSlot) {
                    $tempData[SnapshotCreatorInterface::CACHE_KEY_FIRST_OPPORTUNITY] += array_sum(array_column($results, 2));
                    $tempData[SnapshotCreatorInterface::CACHE_KEY_VERIFIED_IMPRESSION] += array_sum(array_column($results, 3));
                    $tempData[SnapshotCreatorInterface::CACHE_KEY_PASSBACK] += array_sum(array_column($results, 4)) + array_sum(array_column($results, 9));
                    $tempData[SnapshotCreatorInterface::CACHE_KEY_UNVERIFIED_IMPRESSION] += array_sum(array_column($results, 5));
                    $tempData[SnapshotCreatorInterface::CACHE_KEY_BLANK_IMPRESSION] += array_sum(array_column($results, 6));
                    $tempData[SnapshotCreatorInterface::CACHE_KEY_VOID_IMPRESSION] += array_sum(array_column($results, 7));
                    $tempData[SnapshotCreatorInterface::CACHE_KEY_CLICK] += array_sum(array_column($results, 8));
                }
                $cacheKeys = [];
                $keyCount = 0;
            }
        }

        $pipe = $this->cache->multi(Redis::PIPELINE);
        array_walk($cacheKeys, function($keys) use ($pipe) {
            $pipe->mGet($keys);
        });
        $results = $pipe->exec();
        $tempData[SnapshotCreatorInterface::CACHE_KEY_OPPORTUNITY] += array_sum(array_column($results, 0));
        $tempData[SnapshotCreatorInterface::CACHE_KEY_IMPRESSION] += array_sum(array_column($results, 1));

        if (false === $nativeSlot) {
            $tempData[SnapshotCreatorInterface::CACHE_KEY_FIRST_OPPORTUNITY] += array_sum(array_column($results, 2));
            $tempData[SnapshotCreatorInterface::CACHE_KEY_VERIFIED_IMPRESSION] += array_sum(array_column($results, 3));
            $tempData[SnapshotCreatorInterface::CACHE_KEY_PASSBACK] += array_sum(array_column($results, 4)) + array_sum(array_column($results, 9));
            $tempData[SnapshotCreatorInterface::CACHE_KEY_UNVERIFIED_IMPRESSION] += array_sum(array_column($results, 5));
            $tempData[SnapshotCreatorInterface::CACHE_KEY_BLANK_IMPRESSION] += array_sum(array_column($results, 6));
            $tempData[SnapshotCreatorInterface::CACHE_KEY_VOID_IMPRESSION] += array_sum(array_column($results, 7));
            $tempData[SnapshotCreatorInterface::CACHE_KEY_CLICK] += array_sum(array_column($results, 8));
        }

        return $tempData;
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
            $convertedResults[static::$adTagReportKeys[$index]] = $value;
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
            $reports[] = new RonAdTagReportCount($this->formattedDate, $id, $results, $segmentId);
        }

        return $reports;
    }

    public function getRonAdTagReport($ronTagId, $segmentId = null, $hasNativeSlotContainer = false)
    {
        $ronTagKeys = $this->createCacheKeysForRonTag($ronTagId, $segmentId, $hasNativeSlotContainer);

        $results = $this->cache->hMGet(self::REDIS_HASH_EVENT_COUNT, $ronTagKeys);

        return new RonAdTagReportCount($this->formattedDate, $ronTagId, $results, $segmentId);
    }

    public function getRonAdSlotReport($ronAdSlotId, $segmentId = null)
    {
        $ronAdSlotKeys = $this->createCacheKeysForRonAdSlot($ronAdSlotId, $segmentId);

        $results = $this->cache->hMGet(self::REDIS_HASH_EVENT_COUNT, $ronAdSlotKeys);

        $inBannerRequests = $this->getRonInBannerRequestCount($ronAdSlotId, $segmentId);
        $inBannerTimeouts = $this->getRonInBannerTimeoutCount($ronAdSlotId, $segmentId);
        $inBannerImpressions = $this->getRonInBannerImpressionCount($ronAdSlotId, $segmentId);
        $ronSlotCount = new RonAdSlotReportCount($this->formattedDate, $ronAdSlotId, $results, $segmentId);
        $ronSlotCount->setInBannerRequests($inBannerRequests);
        $ronSlotCount->setInBannerTimeouts($inBannerTimeouts);
        $ronSlotCount->setInBannerImpressions($inBannerImpressions);

        return $ronSlotCount;
    }

    /**
     * @inheritdoc
     */
    public function getAccountReport(PublisherInterface $publisher)
    {
        $cacheKeys =[];
        $inBannerCacheKeys =[];

        $tempData = array (
            SnapshotCreatorInterface::CACHE_KEY_SLOT_OPPORTUNITY => 0,
            SnapshotCreatorInterface::CACHE_KEY_HEADER_BID_REQUEST => 0,
            SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_IMPRESSION => 0,
            SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_REQUEST => 0,
            SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_TIMEOUT => 0,
            SnapshotCreatorInterface::CACHE_KEY_OPPORTUNITY => 0,
            SnapshotCreatorInterface::CACHE_KEY_IMPRESSION => 0,
            SnapshotCreatorInterface::CACHE_KEY_PASSBACK => 0,
        );

        $adSlotIds = $this->adSlotManager->getReportableAdSlotIdsForPublisher($publisher);
        if (count($adSlotIds) < 1) {
            return $tempData;
        }

        $keyCount = 0;
        foreach ($adSlotIds as $id) {
            $namespace = $this->getNamespace(self::NAMESPACE_AD_SLOT, $id);
            $cacheKeys[] = array (
                $this->getCacheKey(self::CACHE_KEY_SLOT_OPPORTUNITY, $namespace),
                $this->getCacheKey(self::CACHE_KEY_HB_BID_REQUEST, $namespace)
            );

            $inBannerCacheKeys[] = array (
                $this->getCacheKey(self::CACHE_KEY_IN_BANNER_IMPRESSION, $namespace),
                $this->getCacheKey(self::CACHE_KEY_IN_BANNER_REQUEST, $namespace),
                $this->getCacheKey(self::CACHE_KEY_IN_BANNER_TIMEOUT, $namespace)
            );

            $keyCount++;
            if ($keyCount >= $this->pipelineSizeThreshold) {
                $pipe = $this->cache->multi(Redis::PIPELINE);
                array_walk($cacheKeys, function($keys) use ($pipe) {
                    $pipe->mGet($keys);
                });
                $results = $pipe->exec(); // sequence of output is sequence of slot ids

                $pipe = $this->cache->multi(Redis::PIPELINE);
                array_walk($inBannerCacheKeys, function($keys) use ($pipe) {
                    $pipe->hMGet(!$this->getDataWithDateHour() ? self::REDIS_HASH_IN_BANNER_EVENT_COUNT : $this->getHashFieldDate(), $keys);
                });
                $inBannerResults = $pipe->exec(); // sequence of output is sequence of slot ids

                $tempData[SnapshotCreatorInterface::CACHE_KEY_SLOT_OPPORTUNITY] += array_sum(array_column($results, 0));
                $tempData[SnapshotCreatorInterface::CACHE_KEY_HEADER_BID_REQUEST] += array_sum(array_column($results, 1));
                $tempData[SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_IMPRESSION] += array_sum(array_column($inBannerResults, 0));
                $tempData[SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_REQUEST] += array_sum(array_column($inBannerResults, 1));
                $tempData[SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_TIMEOUT] += array_sum(array_column($inBannerResults, 2));

                $cacheKeys = [];
                $inBannerCacheKeys = [];
                $keyCount = 0;
            }
        }

        $pipe = $this->cache->multi(Redis::PIPELINE);
        array_walk($cacheKeys, function($keys) use ($pipe) {
            $pipe->mGet($keys);
        });
        $results = $pipe->exec(); // sequence of output is sequence of slot ids

        $pipe = $this->cache->multi(Redis::PIPELINE);
        array_walk($inBannerCacheKeys, function($keys) use ($pipe) {
            $pipe->hMGet(!$this->getDataWithDateHour() ? self::REDIS_HASH_IN_BANNER_EVENT_COUNT : $this->getHashFieldDate(), $keys);
        });
        $inBannerResults = $pipe->exec(); // sequence of output is sequence of slot ids

        $tempData[SnapshotCreatorInterface::CACHE_KEY_SLOT_OPPORTUNITY] += array_sum(array_column($results, 0));
        $tempData[SnapshotCreatorInterface::CACHE_KEY_HEADER_BID_REQUEST] += array_sum(array_column($results, 1));
        $tempData[SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_IMPRESSION] += array_sum(array_column($inBannerResults, 0));
        $tempData[SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_REQUEST] += array_sum(array_column($inBannerResults, 1));
        $tempData[SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_TIMEOUT] += array_sum(array_column($inBannerResults, 2));

        $adTagIds = $this->adTagManager->getActiveAdTagsIdsForPublisher($publisher);
        $keyCount = 0;
        $adTagCacheKeys = [];
        foreach($adTagIds as $id) {
            $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $id);
            $adTagCacheKeys[] = array (
                $this->getCacheKey(self::CACHE_KEY_OPPORTUNITY, $namespace),
                $this->getCacheKey(self::CACHE_KEY_IMPRESSION, $namespace),
                $this->getCacheKey(self::CACHE_KEY_PASSBACK, $namespace),
                $this->getCacheKey(self::CACHE_KEY_FALLBACK, $namespace),
            );
            $keyCount++;

            if ($keyCount >= $this->pipelineSizeThreshold) {
                $pipe = $this->cache->multi(Redis::PIPELINE);
                array_walk($adTagCacheKeys, function($keys) use ($pipe) {
                    $pipe->mGet($keys);
                });
                $adTagResults = $pipe->exec();
                $tempData[SnapshotCreatorInterface::CACHE_KEY_OPPORTUNITY] += array_sum(array_column($adTagResults, 0));
                $tempData[SnapshotCreatorInterface::CACHE_KEY_IMPRESSION] += array_sum(array_column($adTagResults, 1));
                $tempData[SnapshotCreatorInterface::CACHE_KEY_PASSBACK] += array_sum(array_column($adTagResults, 2)) + array_sum(array_column($adTagResults, 3));

                $keyCount = 0;
                $adTagCacheKeys = [];
            }
        }

        $pipe = $this->cache->multi(Redis::PIPELINE);
        array_walk($adTagCacheKeys, function($keys) use ($pipe) {
            $pipe->mGet($keys);
        });
        $adTagResults = $pipe->exec();
        $tempData[SnapshotCreatorInterface::CACHE_KEY_OPPORTUNITY] += array_sum(array_column($adTagResults, 0));
        $tempData[SnapshotCreatorInterface::CACHE_KEY_IMPRESSION] += array_sum(array_column($adTagResults, 1));
        $tempData[SnapshotCreatorInterface::CACHE_KEY_PASSBACK] += array_sum(array_column($adTagResults, 2)) + array_sum(array_column($adTagResults, 3));

        return $tempData;
    }

    public function getSiteReportData(SiteInterface $site)
    {
        $cacheKeys =[];
        $inBannerCacheKeys =[];

        $tempData = array (
            SnapshotCreatorInterface::CACHE_KEY_SLOT_OPPORTUNITY => 0,
            SnapshotCreatorInterface::CACHE_KEY_HEADER_BID_REQUEST => 0,
            SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_IMPRESSION => 0,
            SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_REQUEST => 0,
            SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_TIMEOUT => 0,
            SnapshotCreatorInterface::CACHE_KEY_OPPORTUNITY => 0,
            SnapshotCreatorInterface::CACHE_KEY_IMPRESSION => 0,
            SnapshotCreatorInterface::CACHE_KEY_PASSBACK => 0,
        );

        $adSlotIds = $this->adSlotManager->getAdSlotIdsForSite($site);
        if (count($adSlotIds) < 1) {
            return $tempData;
        }

        $keyCount = 0;
        foreach ($adSlotIds as $id) {
            $namespace = $this->getNamespace(self::NAMESPACE_AD_SLOT, $id);
            $cacheKeys[] = array (
                $this->getCacheKey(self::CACHE_KEY_SLOT_OPPORTUNITY, $namespace),
                $this->getCacheKey(self::CACHE_KEY_HB_BID_REQUEST, $namespace)
            );

            $inBannerCacheKeys[] = array (
                $this->getCacheKey(self::CACHE_KEY_IN_BANNER_IMPRESSION, $namespace),
                $this->getCacheKey(self::CACHE_KEY_IN_BANNER_REQUEST, $namespace),
                $this->getCacheKey(self::CACHE_KEY_IN_BANNER_TIMEOUT, $namespace)
            );

            $keyCount++;
            if ($keyCount >= $this->pipelineSizeThreshold) {
                $pipe = $this->cache->multi(Redis::PIPELINE);
                array_walk($cacheKeys, function($keys) use ($pipe) {
                    $pipe->mGet($keys);
                });
                $results = $pipe->exec(); // sequence of output is sequence of slot ids

                $pipe = $this->cache->multi(Redis::PIPELINE);
                array_walk($inBannerCacheKeys, function($keys) use ($pipe) {
                    $pipe->hMGet(!$this->getDataWithDateHour() ? self::REDIS_HASH_IN_BANNER_EVENT_COUNT : $this->getHashFieldDate(), $keys);
                });
                $inBannerResults = $pipe->exec(); // sequence of output is sequence of slot ids

                $tempData[SnapshotCreatorInterface::CACHE_KEY_SLOT_OPPORTUNITY] += array_sum(array_column($results, 0));
                $tempData[SnapshotCreatorInterface::CACHE_KEY_HEADER_BID_REQUEST] += array_sum(array_column($results, 1));
                $tempData[SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_IMPRESSION] += array_sum(array_column($inBannerResults, 0));
                $tempData[SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_REQUEST] += array_sum(array_column($inBannerResults, 1));
                $tempData[SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_TIMEOUT] += array_sum(array_column($inBannerResults, 2));

                $cacheKeys = [];
                $inBannerCacheKeys = [];
                $keyCount = 0;
            }
        }

        $pipe = $this->cache->multi(Redis::PIPELINE);
        array_walk($cacheKeys, function($keys) use ($pipe) {
            $pipe->mGet($keys);
        });
        $results = $pipe->exec(); // sequence of output is sequence of slot ids

        $pipe = $this->cache->multi(Redis::PIPELINE);
        array_walk($inBannerCacheKeys, function($keys) use ($pipe) {
            $pipe->hMGet(!$this->getDataWithDateHour() ? self::REDIS_HASH_IN_BANNER_EVENT_COUNT : $this->getHashFieldDate(), $keys);
        });
        $inBannerResults = $pipe->exec(); // sequence of output is sequence of slot ids

        $tempData[SnapshotCreatorInterface::CACHE_KEY_SLOT_OPPORTUNITY] += array_sum(array_column($results, 0));
        $tempData[SnapshotCreatorInterface::CACHE_KEY_HEADER_BID_REQUEST] += array_sum(array_column($results, 1));
        $tempData[SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_IMPRESSION] += array_sum(array_column($inBannerResults, 0));
        $tempData[SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_REQUEST] += array_sum(array_column($inBannerResults, 1));
        $tempData[SnapshotCreatorInterface::CACHE_KEY_IN_BANNER_TIMEOUT] += array_sum(array_column($inBannerResults, 2));

        $adTagIds = $this->adTagManager->getAdTagIdsForSite($site);
        $keyCount = 0;
        $adTagCacheKeys = [];
        foreach($adTagIds as $id) {
            $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $id);
            $adTagCacheKeys[] = array (
                $this->getCacheKey(self::CACHE_KEY_OPPORTUNITY, $namespace),
                $this->getCacheKey(self::CACHE_KEY_IMPRESSION, $namespace),
                $this->getCacheKey(self::CACHE_KEY_PASSBACK, $namespace),
                $this->getCacheKey(self::CACHE_KEY_FALLBACK, $namespace),
            );
            $keyCount++;

            if ($keyCount >= $this->pipelineSizeThreshold) {
                $pipe = $this->cache->multi(Redis::PIPELINE);
                array_walk($adTagCacheKeys, function($keys) use ($pipe) {
                    $pipe->mGet($keys);
                });
                $adTagResults = $pipe->exec();
                $tempData[SnapshotCreatorInterface::CACHE_KEY_OPPORTUNITY] += array_sum(array_column($adTagResults, 0));
                $tempData[SnapshotCreatorInterface::CACHE_KEY_IMPRESSION] += array_sum(array_column($adTagResults, 1));
                $tempData[SnapshotCreatorInterface::CACHE_KEY_PASSBACK] += array_sum(array_column($adTagResults, 2)) + array_sum(array_column($adTagResults, 3));

                $keyCount = 0;
                $adTagCacheKeys = [];
            }
        }

        $pipe = $this->cache->multi(Redis::PIPELINE);
        array_walk($adTagCacheKeys, function($keys) use ($pipe) {
            $pipe->mGet($keys);
        });
        $adTagResults = $pipe->exec();
        $tempData[SnapshotCreatorInterface::CACHE_KEY_OPPORTUNITY] += array_sum(array_column($adTagResults, 0));
        $tempData[SnapshotCreatorInterface::CACHE_KEY_IMPRESSION] += array_sum(array_column($adTagResults, 1));
        $tempData[SnapshotCreatorInterface::CACHE_KEY_PASSBACK] += array_sum(array_column($adTagResults, 2)) + array_sum(array_column($adTagResults, 3));

        return $tempData;
    }


    /**
     * @inheritdoc
     */
    public function getAccountReports(array $publishers)
    {
        $publisherIds = [];
        $convertedResults =[];
        $accountKeys = [];
        $accountKeyCount = 0;

        foreach ($publishers as $publisher) {
            if ($publisher instanceof PublisherInterface) {
                $publisherIds[] = $publisher->getId();
            }
        }

        foreach ($publisherIds as $id) {
            $cacheKeysForThisAccount = $this->createCacheKeysForAccount($id);
            if ($accountKeyCount === 0) {
                $accountKeyCount = count($cacheKeysForThisAccount);
            }

            foreach ($cacheKeysForThisAccount as $k) {
                $accountKeys[] = $k;
            }
        }

        $results = $this->cache->mGet($accountKeys);
        $totalResultCount = count($results);
        $index = 0;

        foreach($publisherIds as $publisherId) {
            if ($index + $accountKeyCount > $totalResultCount) {
                throw new \RuntimeException('something went wrong with redis fetching multiple keys');
            }

            $singleConvertedResults = [];

            $accountReportKey = 0;
            for($i = $index; $i < $index + $accountKeyCount ; $i ++) {
                $singleConvertedResults[static::$accountReportKeys[$accountReportKey]] = $results[$i];
                $accountReportKey ++;
            }

            $convertedResults[$publisherId] = new AccountReportCount($singleConvertedResults);
            $index += $accountKeyCount;
        }

        return $convertedResults;
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
            $adTagKeys[] = $this->getCacheKey(self::CACHE_KEY_FALLBACK, $namespace);
            $adTagKeys[] = $this->getCacheKey(self::CACHE_KEY_REFRESHES, $namespace);
        }

        return $adTagKeys;
    }

    /**
     * create CacheKey For Account
     *
     * @param $publisherId
     * @return array
     */
    protected function createCacheKeysForAccount($publisherId)
    {
        $namespace = $this->getNamespace(self::NAMESPACE_ACCOUNT, $publisherId);

        $accountKeys = array (
            $this->getCacheKey(self::CACHE_KEY_ACC_OPPORTUNITY, $namespace),
            $this->getCacheKey(self::CACHE_KEY_ACC_SLOT_OPPORTUNITY, $namespace),
            $this->getCacheKey(self::CACHE_KEY_IMPRESSION, $namespace),
            $this->getCacheKey(self::CACHE_KEY_PASSBACK, $namespace),
            $this->getCacheKey(self::CACHE_KEY_FALLBACK, $namespace),
        );

        return $accountKeys;
    }

    protected function getAdTagCacheKeysForAccount(PublisherInterface $publisher)
    {
        $adTagIds = $this->adTagManager->getActiveAdTagsIdsForPublisher($publisher);
        return $this->mapAdTagCacheKey($adTagIds);
    }

    protected function getAdTagCacheKeysForAdSlot(ReportableAdSlotInterface $slot)
    {
        $adTagIds = $this->adTagManager->getAdTagIdsForAdSlot($slot);
        return $this->mapAdTagCacheKey($adTagIds);
    }

    protected function getAdTagCacheKeysForSite(SiteInterface $site)
    {
        $adTagIds = $this->adTagManager->getAdTagIdsForSite($site);
        return $this->mapAdTagCacheKey($adTagIds);
    }

    protected function getAdTagCacheKeysForPlatform()
    {
        $adTagIds = $this->adTagManager->getAllActiveAdTagIds();
        return $this->mapAdTagCacheKey($adTagIds);
    }

    protected function getAdSlotKeysForAccount(PublisherInterface $publisher)
    {
        $adSlotIds = $this->adSlotManager->getReportableAdSlotIdsForPublisher($publisher);
        return $this->mapAdSlotCacheKey($adSlotIds);
    }

    protected function getAdSlotKeysForPlatform()
    {
        $adSlotIds = $this->adSlotManager->allReportableAdSlotIds();
        return $this->mapAdSlotCacheKey($adSlotIds);
    }

    private function mapAdSlotCacheKey(array $ids)
    {
        return array_map(function($id) {
            $namespace = $this->getNamespace(self::NAMESPACE_AD_SLOT, $id);
            return array (
                $this->getCacheKey(self::CACHE_KEY_OPPORTUNITY, $namespace),
                $this->getCacheKey(self::CACHE_KEY_HB_BID_REQUEST, $namespace)
            );
        }, $ids);
    }

    private function mapAdTagCacheKey(array $ids)
    {
        return array_map(function($id) {
            $namespace = $this->getNamespace(self::NAMESPACE_AD_TAG, $id);
            return array (
                $this->getCacheKey(self::CACHE_KEY_OPPORTUNITY, $namespace),
                $this->getCacheKey(self::CACHE_KEY_IMPRESSION, $namespace),
                $this->getCacheKey(self::CACHE_KEY_PASSBACK, $namespace),
                $this->getCacheKey(self::CACHE_KEY_FALLBACK, $namespace),
            );
        }, $ids);
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

    public function getCacheKey($key, $namespace)
    {
        $keyFormat = '%s:%s:%s'; // opportunities:adtag_100:2018040901
        return sprintf($keyFormat, $key, $namespace, $this->formattedDate);
    }

    /**
     * @return mixed
     */
    private function getHashFieldDate()
    {
        $date = $this->getDate()->format('ymd');
        //Build new hash field
        return sprintf("%s:%s", self::REDIS_HASH_IN_BANNER_EVENT_COUNT, $date);
    }
}