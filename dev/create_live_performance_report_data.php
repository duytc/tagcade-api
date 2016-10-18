<?php

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();

$adSlotManager = $container->get('tagcade.domain_manager.ad_slot');

$testEventCounter = new \Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter($adSlotManager->all());
$testEventCounter->refreshTestData();

$redis = new RedisArray(['localhost']);
$cache = new Tagcade\Cache\Legacy\Cache\RedisArrayCache();
$cache->setRedis($redis);

$cacheEventCounter = new \Tagcade\Service\Report\PerformanceReport\Display\Counter\CacheEventCounter($cache);
$cacheEventCounter->setDate(new DateTime('yesterday'));

foreach($testEventCounter->getAdSlotData() as $slotId => $slotData) {
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
        $cache->save(
            $cacheEventCounter->getCacheKey(
                $cacheEventCounter::CACHE_KEY_RTB_IMPRESSION,
                $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_AD_SLOT, $slotId)
            ),
            $slotData[$testEventCounter::KEY_RTB_IMPRESSION]
        );
    }
    
    $cache->save(
        $cacheEventCounter->getCacheKey(
            $cacheEventCounter::CACHE_KEY_HB_BID_REQUEST,
            $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_AD_SLOT, $slotId)
        ),
        $slotData[$testEventCounter::KEY_HB_BID_REQUEST]
    );

    unset($slotId, $slotData);
}
// generate for ron ad slot
foreach($testEventCounter->getRonAdSlotData() as $ronSlotId => $ronSlotData) {
    $namespace = $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_RON_AD_SLOT, $ronSlotId);
    $cache->hSave($cacheEventCounter::REDIS_HASH_EVENT_COUNT,
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_SLOT_OPPORTUNITY, $namespace ),
        $ronSlotData[$testEventCounter::KEY_SLOT_OPPORTUNITY]
    );

    unset($ronSlotId, $ronSlotData);
}

// generate for ron ad tag
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