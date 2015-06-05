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
$cacheEventCounter->setDate(new DateTime('today'));

foreach($testEventCounter->getAdSlotData() as $slotId => $slotData) {
    $cache->save(
        $cacheEventCounter->getCacheKey(
            $cacheEventCounter::CACHE_KEY_SLOT_OPPORTUNITY,
            $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_AD_SLOT, $slotId)
        ),
        $slotData[$testEventCounter::KEY_SLOT_OPPORTUNITY]
    );

    unset($slotId, $slotData);
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

    unset($tagId, $tagData);
}