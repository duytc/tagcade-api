<?php

date_default_timezone_set('Asia/Bangkok');

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();

$adSlotManager = $container->get('tagcade.domain_manager.ad_slot');

$testEventCounter = new \Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter($adSlotManager->all());
$testEventCounter->refreshTestData();

$redis = new Redis();
$redis->connect('localhost');

$cache = new \Doctrine\Common\Cache\RedisCache();
$cache->setRedis($redis);

$cacheEventCounter = new \Tagcade\Service\Report\PerformanceReport\Display\Counter\CacheEventCounter($cache);

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

$redis->close();