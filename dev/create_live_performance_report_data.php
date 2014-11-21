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

$cache = new Tagcade\Legacy\Cache\RedisArrayCache();
$cache->setRedis($redis);

$cacheEventCounter = new \Tagcade\Service\Report\PerformanceReport\Display\Counter\CacheEventCounter($cache);
$cacheEventCounter->setDate(new DateTime('today'));

foreach($testEventCounter->getAdSlotData() as $slotId => $slotData) {
    $cache->save(
        $cacheEventCounter->getCacheKey($cacheEventCounter::SLOT_OPPORTUNITY, $slotId),
        $slotData[$testEventCounter::SLOT_OPPORTUNITIES]
    );

    unset($slotId, $slotData);
}

foreach($testEventCounter->getAdTagData() as $tagId => $tagData) {
    $cache->save(
        $cacheEventCounter->getCacheKey($cacheEventCounter::OPPORTUNITY, $tagId),
        $tagData[$testEventCounter::OPPORTUNITIES]
    );

    $cache->save(
        $cacheEventCounter->getCacheKey($cacheEventCounter::IMPRESSION, $tagId),
        $tagData[$testEventCounter::IMPRESSIONS]
    );

    $cache->save(
        $cacheEventCounter->getCacheKey($cacheEventCounter::FALLBACK, $tagId),
        $tagData[$testEventCounter::PASSBACKS]
    );

    unset($tagId, $tagData);
}