<?php

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();

$adSlotManager = $container->get('tagcade.domain_manager.ad_slot');
$adTagManager = $container->get('tagcade.domain_manager.ad_tag');
$allAdSLot = $adSlotManager->all();

$allAdSLotMap = [];
/** @var \Tagcade\Model\Core\BaseAdSlotInterface[] $allAdSLot */
foreach ($allAdSLot as $adSlot) {
    $allAdSLotMap[$adSlot->getId()] = $adSlot;
}

writeln('### Start creating test live data for performance reports ###');

$testEventCounter = new \Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter($allAdSLot);
$testEventCounter->refreshTestData();

$host = $container->getParameter('tc.redis.app_cache.host'); // or manually set value as tagcade.dev or localhost
$port = $container->getParameter('tc.redis.app_cache.port'); // or manually set value as 6379
$redis = new Redis();
$redis->connect($host, $port);
$cache = new Tagcade\Cache\Legacy\Cache\RedisArrayCache();
$cache->setRedis($redis);

$cacheEventCounter = new \Tagcade\Service\Report\PerformanceReport\Display\Counter\CacheEventCounter($cache,
    $adTagManager,
    $adSlotManager,
    $container->getParameter('tc.report.performance.event_counter.redis_pipeline_size_threshold')
    );
$cacheEventCounter->setDate(new DateTime('yesterday'));

writeln('### creating test live data for account ###');
foreach($testEventCounter->getAccountData() as $publisherId => $accountData) {
    writeln('###    ... account #' . $publisherId . ' ###');
    if (array_key_exists($testEventCounter::CACHE_KEY_ACC_SLOT_OPPORTUNITY, $accountData)) {
        $cache->save(
            $cacheEventCounter->getCacheKey(
                $cacheEventCounter::CACHE_KEY_ACC_SLOT_OPPORTUNITY,
                $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_ACCOUNT, $publisherId)
            ),
            $accountData[$testEventCounter::CACHE_KEY_ACC_SLOT_OPPORTUNITY]
        );
    }

    if (array_key_exists($testEventCounter::CACHE_KEY_ACC_OPPORTUNITY, $accountData)) {
        $cache->save(
            $cacheEventCounter->getCacheKey(
                $cacheEventCounter::CACHE_KEY_ACC_OPPORTUNITY,
                $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_ACCOUNT, $publisherId)
            ),
            $accountData[$testEventCounter::CACHE_KEY_ACC_OPPORTUNITY]
        );
    }

    if (array_key_exists($testEventCounter::KEY_IMPRESSION, $accountData)) {
        $cache->save(
            $cacheEventCounter->getCacheKey(
                $cacheEventCounter::CACHE_KEY_IMPRESSION,
                $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_ACCOUNT, $publisherId)
            ),
            $accountData[$testEventCounter::KEY_IMPRESSION]
        );
    }

    if (array_key_exists($testEventCounter::KEY_HB_BID_REQUEST, $accountData)) {
        $cache->save(
            $cacheEventCounter->getCacheKey(
                $cacheEventCounter::CACHE_KEY_HB_BID_REQUEST,
                $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_ACCOUNT, $publisherId)
            ),
            $accountData[$testEventCounter::KEY_HB_BID_REQUEST]
        );
    }

    if (array_key_exists($testEventCounter::KEY_PASSBACK, $accountData)) {
        $cache->save(
            $cacheEventCounter->getCacheKey(
                $cacheEventCounter::CACHE_KEY_PASSBACK,
                $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_ACCOUNT, $publisherId)
            ),
            $accountData[$testEventCounter::KEY_PASSBACK]
        );
    }

    if (array_key_exists($testEventCounter::KEY_RTB_IMPRESSION, $accountData)) {
        $cache->hSave(
            $cacheEventCounter::REDIS_HASH_RTB_EVENT_COUNT,
            $cacheEventCounter->getCacheKey(
                $cacheEventCounter::CACHE_KEY_RTB_IMPRESSION,
                $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_ACCOUNT, $publisherId)
            ),
            $accountData[$testEventCounter::KEY_RTB_IMPRESSION]
        );
    }

    if (array_key_exists($testEventCounter::KEY_IN_BANNER_IMPRESSIONS, $accountData)) {
        $cache->hSave(
            $cacheEventCounter::REDIS_HASH_IN_BANNER_EVENT_COUNT,
            $cacheEventCounter->getCacheKey(
                $cacheEventCounter::CACHE_KEY_IN_BANNER_IMPRESSION,
                $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_ACCOUNT, $publisherId)
            ),
            $accountData[$testEventCounter::KEY_IN_BANNER_IMPRESSIONS]
        );
    }

    if (array_key_exists($testEventCounter::KEY_IN_BANNER_REQUESTS, $accountData)) {
        $cache->hSave(
            $cacheEventCounter::REDIS_HASH_IN_BANNER_EVENT_COUNT,
            $cacheEventCounter->getCacheKey(
                $cacheEventCounter::CACHE_KEY_IN_BANNER_REQUEST,
                $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_ACCOUNT, $publisherId)
            ),
            $accountData[$testEventCounter::KEY_IN_BANNER_REQUESTS]
        );
    }

    if (array_key_exists($testEventCounter::KEY_IN_BANNER_TIMEOUT, $accountData)) {
        $cache->hSave(
            $cacheEventCounter::REDIS_HASH_IN_BANNER_EVENT_COUNT,
            $cacheEventCounter->getCacheKey(
                $cacheEventCounter::CACHE_KEY_IN_BANNER_TIMEOUT,
                $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_ACCOUNT, $publisherId)
            ),
            $accountData[$testEventCounter::KEY_IN_BANNER_TIMEOUT]
        );
    }
}

writeln('### creating test live data for all ad slots ###');
foreach($testEventCounter->getAdSlotData() as $slotId => $slotData) {
    $adSlot = findAdSlot($allAdSLotMap, $slotId);

    if (array_key_exists($testEventCounter::KEY_SLOT_OPPORTUNITY, $slotData)) {
        $cache->save(
            $cacheEventCounter->getCacheKey(
                $cacheEventCounter::CACHE_KEY_SLOT_OPPORTUNITY,
                $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_AD_SLOT, $slotId)
            ),
            $slotData[$testEventCounter::KEY_SLOT_OPPORTUNITY]
        );
    }

    if (array_key_exists($testEventCounter::KEY_RTB_IMPRESSION, $slotData)) {
        $cache->hSave(
            $cacheEventCounter::REDIS_HASH_RTB_EVENT_COUNT,
            $cacheEventCounter->getCacheKey(
                $cacheEventCounter::CACHE_KEY_RTB_IMPRESSION,
                $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_AD_SLOT, $slotId)
            ),
            $slotData[$testEventCounter::KEY_RTB_IMPRESSION]
        );
    }

    if($adSlot->getSite()->getPublisher()->hasInBannerModule()) {
        if (array_key_exists($testEventCounter::KEY_IN_BANNER_REQUESTS, $slotData)) {
            $cache->hSave(
                $cacheEventCounter::REDIS_HASH_IN_BANNER_EVENT_COUNT,
                $cacheEventCounter->getCacheKey(
                    $cacheEventCounter::CACHE_KEY_IN_BANNER_REQUEST,
                    $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_AD_SLOT, $slotId)
                ),
                $slotData[$testEventCounter::KEY_IN_BANNER_REQUESTS]
            );
        }

        if (array_key_exists($testEventCounter::KEY_IN_BANNER_IMPRESSIONS, $slotData)) {
            $cache->hSave(
                $cacheEventCounter::REDIS_HASH_IN_BANNER_EVENT_COUNT,
                $cacheEventCounter->getCacheKey(
                    $cacheEventCounter::CACHE_KEY_IN_BANNER_IMPRESSION,
                    $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_AD_SLOT, $slotId)
                ),
                $slotData[$testEventCounter::KEY_IN_BANNER_IMPRESSIONS]
            );
        }

        if (array_key_exists($testEventCounter::KEY_IN_BANNER_TIMEOUT, $slotData)) {
            $cache->hSave(
                $cacheEventCounter::REDIS_HASH_IN_BANNER_EVENT_COUNT,
                $cacheEventCounter->getCacheKey(
                    $cacheEventCounter::CACHE_KEY_IN_BANNER_TIMEOUT,
                    $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_AD_SLOT, $slotId)
                ),
                $slotData[$testEventCounter::KEY_IN_BANNER_TIMEOUT]
            );
        }

    }

    if (array_key_exists($testEventCounter::KEY_HB_BID_REQUEST, $slotData)) {
        $cache->save(
            $cacheEventCounter->getCacheKey(
                $cacheEventCounter::CACHE_KEY_HB_BID_REQUEST,
                $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_AD_SLOT, $slotId)
            ),
            $slotData[$testEventCounter::KEY_HB_BID_REQUEST]
        );
    }

    unset($slotId, $slotData);
}

// generate for ron ad slot
writeln('### creating test live data for all ron ad slots ###');
foreach($testEventCounter->getRonAdSlotData() as $ronSlotId => $ronSlotData) {
    $namespace = $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_RON_AD_SLOT, $ronSlotId);
    if (array_key_exists($testEventCounter::KEY_SLOT_OPPORTUNITY, $ronSlotData)) {
        $cache->hSave($cacheEventCounter::REDIS_HASH_EVENT_COUNT,
            $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_SLOT_OPPORTUNITY, $namespace ),
            $ronSlotData[$testEventCounter::KEY_SLOT_OPPORTUNITY]
        );
    }

    if (array_key_exists($testEventCounter::KEY_RTB_IMPRESSION, $ronSlotData)) {
        $cache->hSave($cacheEventCounter::REDIS_HASH_RTB_EVENT_COUNT,
            $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_RTB_IMPRESSION, $namespace ),
            $ronSlotData[$testEventCounter::KEY_RTB_IMPRESSION]
        );
    }

    unset($ronSlotId, $ronSlotData);
}

// generate for ron ad tag
writeln('### creating test live data for all ron ad tags ###');
foreach($testEventCounter->getRonAdTagData() as $ronSlotId => $ronTagData) {
    $namespace = $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_RON_AD_TAG, $ronSlotId);
    $cache->hSave(
        $cacheEventCounter::REDIS_HASH_EVENT_COUNT,
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_OPPORTUNITY, $namespace),
        $ronTagData[$testEventCounter::KEY_OPPORTUNITY]
    );

    $cache->hSave(
        $cacheEventCounter::REDIS_HASH_EVENT_COUNT,
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_FIRST_OPPORTUNITY, $namespace),
        $ronTagData[$testEventCounter::KEY_FIRST_OPPORTUNITY]
    );

    $cache->hSave(
        $cacheEventCounter::REDIS_HASH_EVENT_COUNT,
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_IMPRESSION, $namespace),
        $ronTagData[$testEventCounter::KEY_IMPRESSION]
    );

    $cache->hSave(
        $cacheEventCounter::REDIS_HASH_EVENT_COUNT,
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_VERIFIED_IMPRESSION, $namespace),
        $ronTagData[$testEventCounter::KEY_VERIFIED_IMPRESSION]
    );

    $cache->hSave(
        $cacheEventCounter::REDIS_HASH_EVENT_COUNT,
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_UNVERIFIED_IMPRESSION, $namespace),
        $ronTagData[$testEventCounter::KEY_UNVERIFIED_IMPRESSION]
    );

    $cache->hSave(
        $cacheEventCounter::REDIS_HASH_EVENT_COUNT,
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_BLANK_IMPRESSION, $namespace),
        $ronTagData[$testEventCounter::KEY_BLANK_IMPRESSION]
    );

    $cache->hSave(
        $cacheEventCounter::REDIS_HASH_EVENT_COUNT,
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_PASSBACK, $namespace),
        $ronTagData[$testEventCounter::KEY_PASSBACK]
    );

    $cache->hSave(
        $cacheEventCounter::REDIS_HASH_EVENT_COUNT,
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_VOID_IMPRESSION, $namespace),
        $ronTagData[$testEventCounter::KEY_VOID_IMPRESSION]
    );

    $cache->hSave(
        $cacheEventCounter::REDIS_HASH_EVENT_COUNT,
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_CLICK, $namespace),
        $ronTagData[$testEventCounter::KEY_CLICK]
    );
    unset($ronSlotId, $ronTagData);
}

// generate for ron ad tag segment
writeln('### creating test live data for all ron ad tag-segments ###');
foreach($testEventCounter->getRonAdTagSegmentData() as $ronSlotId => $segments) {
    foreach ($segments as $segmentId => $ronTagSegmentData) {
        $namespace = $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_RON_AD_TAG, $ronSlotId, $cacheEventCounter::NAMESPACE_APPEND_SEGMENT, $segmentId);
        $cache->hSave(
            $cacheEventCounter::REDIS_HASH_EVENT_COUNT,
            $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_OPPORTUNITY, $namespace),
            $ronTagSegmentData[$testEventCounter::KEY_OPPORTUNITY]
        );

        $cache->hSave(
            $cacheEventCounter::REDIS_HASH_EVENT_COUNT,
            $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_FIRST_OPPORTUNITY, $namespace),
            $ronTagSegmentData[$testEventCounter::KEY_FIRST_OPPORTUNITY]
        );

        $cache->hSave(
            $cacheEventCounter::REDIS_HASH_EVENT_COUNT,
            $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_IMPRESSION, $namespace),
            $ronTagSegmentData[$testEventCounter::KEY_IMPRESSION]
        );

        $cache->hSave(
            $cacheEventCounter::REDIS_HASH_EVENT_COUNT,
            $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_VERIFIED_IMPRESSION, $namespace),
            $ronTagSegmentData[$testEventCounter::KEY_VERIFIED_IMPRESSION]
        );

        $cache->hSave(
            $cacheEventCounter::REDIS_HASH_EVENT_COUNT,
            $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_UNVERIFIED_IMPRESSION, $namespace),
            $ronTagSegmentData[$testEventCounter::KEY_UNVERIFIED_IMPRESSION]
        );

        $cache->hSave(
            $cacheEventCounter::REDIS_HASH_EVENT_COUNT,
            $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_BLANK_IMPRESSION, $namespace),
            $ronTagSegmentData[$testEventCounter::KEY_BLANK_IMPRESSION]
        );

        $cache->hSave(
            $cacheEventCounter::REDIS_HASH_EVENT_COUNT,
            $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_PASSBACK, $namespace),
            $ronTagSegmentData[$testEventCounter::KEY_PASSBACK]
        );

        $cache->hSave(
            $cacheEventCounter::REDIS_HASH_EVENT_COUNT,
            $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_VOID_IMPRESSION, $namespace),
            $ronTagSegmentData[$testEventCounter::KEY_VOID_IMPRESSION]
        );

        $cache->hSave(
            $cacheEventCounter::REDIS_HASH_EVENT_COUNT,
            $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_CLICK, $namespace),
            $ronTagSegmentData[$testEventCounter::KEY_CLICK]
        );
        unset($segmentId, $ronTagSegmentData);
    }


    unset($ronSlotId, $segments);
}

// generate for ron ad slot segment
writeln('### creating test live data for all ron ad slot-segments ###');
$a = $testEventCounter->getRonAdSlotSegmentData();
foreach($testEventCounter->getRonAdSlotSegmentData() as $ronSlotId => $segments) {
    foreach ($segments as $segmentId => $segmentSlotData) {
        $namespace = $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_RON_AD_SLOT, $ronSlotId, $cacheEventCounter::NAMESPACE_APPEND_SEGMENT, $segmentId);
        $cache->hSave($cacheEventCounter::REDIS_HASH_EVENT_COUNT,
            $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_SLOT_OPPORTUNITY, $namespace),
            $segmentSlotData[$testEventCounter::KEY_SLOT_OPPORTUNITY]
        );

        unset($segmentId, $segmentSlotData);
    }

    unset($ronSlotId, $segments);
}

writeln('### creating test live data for all ad tags ###');
foreach($testEventCounter->getAdTagData() as $tagId => $tagData) {
    $namespace = $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_AD_TAG, $tagId);

    $cache->save(
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_OPPORTUNITY, $namespace),
        $tagData[$testEventCounter::KEY_OPPORTUNITY]
    );

    $cache->save(
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_FIRST_OPPORTUNITY, $namespace),
        $tagData[$testEventCounter::KEY_FIRST_OPPORTUNITY]
    );

    $cache->save(
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_IMPRESSION, $namespace),
        $tagData[$testEventCounter::KEY_IMPRESSION]
    );

    $cache->save(
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_VERIFIED_IMPRESSION, $namespace),
        $tagData[$testEventCounter::KEY_VERIFIED_IMPRESSION]
    );

    $cache->save(
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_UNVERIFIED_IMPRESSION, $namespace),
        $tagData[$testEventCounter::KEY_UNVERIFIED_IMPRESSION]
    );

    $cache->save(
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_BLANK_IMPRESSION, $namespace),
        $tagData[$testEventCounter::KEY_BLANK_IMPRESSION]
    );

    $cache->save(
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_PASSBACK, $namespace),
        $tagData[$testEventCounter::KEY_PASSBACK]
    );

    $cache->save(
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_VOID_IMPRESSION, $namespace),
        $tagData[$testEventCounter::KEY_VOID_IMPRESSION]
    );

    $cache->save(
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_CLICK, $namespace),
        $tagData[$testEventCounter::KEY_CLICK]
    );
    unset($tagId, $tagData);
}

writeln('### Finished creating test live data for performance reports ###');

function writeln($str)
{
    echo $str . PHP_EOL;
}

/**
 * @param \Tagcade\Model\Core\BaseAdSlotInterface[] $allAdSLotMap
 * @param $slotId
 * @return \Tagcade\Model\Core\BaseAdSlotInterface || false
 */
function findAdSlot(array $allAdSLotMap, $slotId) {
    if (array_key_exists($slotId, $allAdSLotMap)) {
        return $allAdSLotMap[$slotId];
    }

    return false;
}