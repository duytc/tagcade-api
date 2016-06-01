<?php

use Tagcade\Service\Report\RtbReport\Counter\RtbCacheEventCounter;
use Tagcade\Service\Report\RtbReport\Counter\RtbTestEventCounter;

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
$container = $kernel->getContainer();

$adSlotManager = $container->get('tagcade.domain_manager.ad_slot');
$ronAdSlotManager = $container->get('tagcade.domain_manager.ron_ad_slot');

// TODO: use $adSlotManager->allReportableRtbAdSlots() instead of
$reportableAdSlots = $adSlotManager->allReportableAdSlots();
$ronAdSlots = $ronAdSlotManager->all();
$rtbTestEventCounter = new RtbTestEventCounter($reportableAdSlots, $ronAdSlots);
$rtbTestEventCounter->setDate(new DateTime('today'));

writeln('### Start creating test live data for all rtb ad slots ###');

writeln('   --> Start refreshing random test data');
writeln('       ...');
$rtbTestEventCounter->refreshTestData();
writeln('   --> Finished refreshing random test data');

writeln('   --> Start preparing redis cache ###');
writeln('       ...');
$redis = new RedisArray(['localhost']);
$cache = new Tagcade\Cache\Legacy\Cache\RedisArrayCache();
$cache->setRedis($redis);
writeln('   --> Finished preparing redis cache ###');

writeln('   --> Start saving data to redis ###');
writeln('       ...');
$rtbCacheEventCounter = new RtbCacheEventCounter($cache);

// generate for rtb ad slot
foreach ($rtbTestEventCounter->getAdSlotData() as $slotId => $slotData) {
    writeln('       ... ad slot #' . $slotId);
    $namespace = $rtbCacheEventCounter->getNamespace(RtbCacheEventCounter::NAMESPACE_AD_SLOT, $slotId);

    // save opportunities
    $cache->hSave(
        $hash = RtbCacheEventCounter::REDIS_HASH_RTB_EVENT_COUNT,
        $field = $rtbCacheEventCounter->getCacheKey(RtbCacheEventCounter::CACHE_KEY_SLOT_OPPORTUNITY, $namespace),
        $data = $slotData[$field]
    );

    // save impressions
    $cache->hSave(
        $hash = RtbCacheEventCounter::REDIS_HASH_RTB_EVENT_COUNT,
        $field = $rtbCacheEventCounter->getCacheKey(RtbCacheEventCounter::CACHE_KEY_IMPRESSION, $namespace),
        $data = $slotData[$field]
    );

    // save price
    $cache->hSave(
        $hash = RtbCacheEventCounter::REDIS_HASH_RTB_EVENT_COUNT,
        $field = $rtbCacheEventCounter->getCacheKey(RtbCacheEventCounter::CACHE_KEY_PRICE, $namespace),
        $data = $slotData[$field]
    );

    unset($slotId, $slotData);
}

// generate for rtb ron ad slot, rtb ron ad slot segment
foreach ($rtbTestEventCounter->getRonAdSlotData() as $ronSlotId => $ronSlotData) {
    writeln('       ... ron ad slot #' . $ronSlotId);
    $namespace = $rtbCacheEventCounter->getNamespace(RtbCacheEventCounter::NAMESPACE_RON_AD_SLOT, $ronSlotId);

    // save opportunities
    $cache->hSave(
        $hash = RtbCacheEventCounter::REDIS_HASH_RTB_EVENT_COUNT,
        $field = $rtbCacheEventCounter->getCacheKey(RtbCacheEventCounter::CACHE_KEY_SLOT_OPPORTUNITY, $namespace),
        $data = $ronSlotData[$field]
    );

    // save impressions
    $cache->hSave(
        $hash = RtbCacheEventCounter::REDIS_HASH_RTB_EVENT_COUNT,
        $field = $rtbCacheEventCounter->getCacheKey(RtbCacheEventCounter::CACHE_KEY_IMPRESSION, $namespace),
        $data = $ronSlotData[$field]
    );

    // save price
    $cache->hSave(
        $hash = RtbCacheEventCounter::REDIS_HASH_RTB_EVENT_COUNT,
        $field = $rtbCacheEventCounter->getCacheKey(RtbCacheEventCounter::CACHE_KEY_PRICE, $namespace),
        $data = $ronSlotData[$field]
    );

    // generate for all segments of this rtb ron ad slot
    foreach ($rtbTestEventCounter->getRonAdSlotSegmentData()[$ronSlotId] as $segmentId => $ronSlotSegmentData) {
        writeln('       ... ... segment #' . $segmentId);
        $namespace = $rtbCacheEventCounter->getNamespace(RtbCacheEventCounter::NAMESPACE_RON_AD_SLOT, $ronSlotId, RtbCacheEventCounter::NAMESPACE_SEGMENT, $segmentId);

        // save opportunities
        $cache->hSave(
            $hash = RtbCacheEventCounter::REDIS_HASH_RTB_EVENT_COUNT,
            $field = $rtbCacheEventCounter->getCacheKey(RtbCacheEventCounter::CACHE_KEY_SLOT_OPPORTUNITY, $namespace),
            $data = $ronSlotSegmentData[$field]
        );

        // save impressions
        $cache->hSave(
            $hash = RtbCacheEventCounter::REDIS_HASH_RTB_EVENT_COUNT,
            $field = $rtbCacheEventCounter->getCacheKey(RtbCacheEventCounter::CACHE_KEY_IMPRESSION, $namespace),
            $data = $ronSlotSegmentData[$field]
        );

        // save price
        $cache->hSave(
            $hash = RtbCacheEventCounter::REDIS_HASH_RTB_EVENT_COUNT,
            $field = $rtbCacheEventCounter->getCacheKey(RtbCacheEventCounter::CACHE_KEY_PRICE, $namespace),
            $data = $ronSlotSegmentData[$field]
        );

        unset($segmentId, $ronSlotSegmentData);
    }

    unset($ronSlotId, $ronSlotData);
}

writeln('   --> Finished saving data to redis ###');

writeln('### Finished creating test live data for all rtb ad slots ###');

function writeln($str)
{
    echo $str . PHP_EOL;
}