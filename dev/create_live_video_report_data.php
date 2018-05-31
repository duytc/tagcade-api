<?php

use Symfony\Component\DependencyInjection\ContainerInterface;
use Tagcade\DomainManager\VideoDemandAdTagManagerInterface;
use Tagcade\DomainManager\VideoWaterfallTagManagerInterface;
use Tagcade\Service\Report\VideoReport\Counter\VideoCacheEventCounter;
use Tagcade\Service\Report\VideoReport\Counter\VideoTestEventCounter;

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

/** @var ContainerInterface $container */
$container = $kernel->getContainer();

/** @var VideoWaterfallTagManagerInterface $videoWaterfallTagManager */
$videoWaterfallTagManager = $container->get('tagcade.domain_manager.video_waterfall_tag');

/** @var VideoDemandAdTagManagerInterface $videoDemandAdTagManager */
$videoDemandAdTagManager = $container->get('tagcade.domain_manager.video_demand_ad_tag');
$dateToRotate = new DateTime('now');
$videoTestEventCounter = new VideoTestEventCounter($videoWaterfallTagManager->all(), $videoDemandAdTagManager);
$videoTestEventCounter->setDate($dateToRotate);

writeln('');
writeln('### Start creating test live data for all video ad tag ###');

writeln('------------------------------------------------------------------------------------------------------------');
writeln('   --> Start refreshing random test data');
writeln('       ...');

$minAdTagRequests = 10000;
$maxAdTagRequests = 100000;
$videoTestEventCounter->refreshTestData($minAdTagRequests, $maxAdTagRequests); // date is set above as 'today'

writeln('   --> Finished refreshing random test data');
writeln('------------------------------------------------------------------------------------------------------------');

writeln('');

writeln('   --> Start preparing redis cache');
writeln('       ...');

$cache = $container->get('tagcade.cache.app_cache');
// set no serializer to make sure value is same as come from event processor module, where value is not serialized
// e.g HGETALL video_event_processor:event_count > clicks:demand_tag_39:170804 "8149" instead of clicks:demand_tag_39:170804 "i:8149;"
$cache->getRedis()->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);

writeln('   --> Finished preparing redis cache');
writeln('------------------------------------------------------------------------------------------------------------');

writeln('');

writeln('   --> Start saving data to redis');
writeln('       ...');
$videoCacheEventCounter = new VideoCacheEventCounter($cache);
$videoCacheEventCounter->setDate($dateToRotate);

// generate for video ad tag for full day
$originalDate = $videoCacheEventCounter->getDate();
foreach ($videoTestEventCounter->getAllVideoWaterfallTagsData() as $videoAdTagId => $videoAdTagData) {
    writeln('       ... video ad tag #' . $videoAdTagId);
    $namespace = $videoCacheEventCounter->getNamespace(VideoCacheEventCounter::NAMESPACE_WATERFALL_AD_TAG, $videoAdTagId);
    saveVideoWaterfallTagsData($videoCacheEventCounter, $container, $namespace, $videoAdTagData);
    unset($videoAdTagId, $videoAdTagData);
}

// generate for video ad tag for for 24 hour
$videoCacheEventCounter->setDataWithDateHour(true);
foreach ($videoTestEventCounter->getAllVideoWaterfallTagsDataHourly() as $videoAdTagId => $videoAdTagDataHourly) {
    writeln('       ... video ad tag hourly#' . $videoAdTagId);
    foreach ($videoAdTagDataHourly as $hour => $videoAdTagDataHourlyItem) {
        $dataWithDateHour = clone $originalDate;
        if (!$dataWithDateHour instanceof DateTime){
            continue;
        }

        // set hour
        $dataWithDateHour->setTime($hour, 0);
        $videoCacheEventCounter->setDate($dataWithDateHour);
        $namespace = $videoCacheEventCounter->getNamespace(VideoCacheEventCounter::NAMESPACE_WATERFALL_AD_TAG, $videoAdTagId);
        saveVideoWaterfallTagsData($videoCacheEventCounter, $container, $namespace, $videoAdTagDataHourlyItem, $originalDate);
    }

    unset($videoAdTagId, $videoAdTagData, $videoAdTagDataHourlyItem, $videoAdTagDataHourly);
}
// set date again to make sure that can get right dateFormatter based on $dataWithDateHour
$videoCacheEventCounter->setDataWithDateHour(false);
$videoCacheEventCounter->setDate($originalDate);

// generate for video ad source
foreach ($videoTestEventCounter->getAllVideoDemandAdTagsData() as $videoAdSourceId => $videoAdSourceData) {
    writeln('       ... video ad source #' . $videoAdSourceId);
    $namespace = $videoCacheEventCounter->getNamespace(VideoCacheEventCounter::NAMESPACE_DEMAND_AD_TAG, $videoAdSourceId);

    saveVideoDemandTagsData($videoCacheEventCounter, $container, $namespace, $videoAdSourceData);
    unset($videoAdSourceId, $videoAdSourceData);
}

// generate for video ad tag for full day
$videoCacheEventCounter->setDataWithDateHour(true);
foreach ($videoTestEventCounter->getAllVideoDemandAdTagsDataHourly() as $videoAdSourceId => $videoAdSourceDataHourly) {
    writeln('       ... video ad source hourly #' . $videoAdSourceId);
    foreach ($videoAdSourceDataHourly as $hour => $videoAdSourceDataHourlyItem) {
        $dataWithDateHour = clone $originalDate;
        if (!$dataWithDateHour instanceof DateTime){
            continue;
        }

        // set hour
        $dataWithDateHour->setTime($hour, 0);
        $videoCacheEventCounter->setDate($dataWithDateHour);
        $namespace = $videoCacheEventCounter->getNamespace(VideoCacheEventCounter::NAMESPACE_DEMAND_AD_TAG, $videoAdSourceId);
        saveVideoDemandTagsData($videoCacheEventCounter, $container, $namespace, $videoAdSourceDataHourlyItem, $originalDate);
    }

    unset($videoAdSourceId, $videoAdSourceDataHourly, $videoAdSourceDataHourlyItem, $hour);
}
// set date again to make sure that can get right dateFormatter based on $dataWithDateHour
$videoCacheEventCounter->setDataWithDateHour(false);
$videoCacheEventCounter->setDate($originalDate);

writeln('   --> Finished saving data to redis ###');
writeln('------------------------------------------------------------------------------------------------------------');

writeln('### Finished creating test live data for all video ad slots ###');

/**
 * @param VideoCacheEventCounter $videoCacheEventCounter
 * @param ContainerInterface $container
 * @param $namespace
 * @param $videoAdTagData
 * @param null $originalDate
 */
function saveVideoWaterfallTagsData(VideoCacheEventCounter $videoCacheEventCounter, ContainerInterface $container, $namespace, $videoAdTagData, $originalDate = null) {

    $cache = $container->get('tagcade.cache.app_cache');

    $hash = !empty($originalDate) ? getHashFieldDate(VideoCacheEventCounter::REDIS_HASH_VIDEO_EVENT_COUNT, $originalDate) : VideoCacheEventCounter::REDIS_HASH_VIDEO_EVENT_COUNT;
    // save requests
    $cache->hSave(
    $hash,
    $field = $videoCacheEventCounter->getCacheKey(VideoCacheEventCounter::CACHE_KEY_REQUESTS, $namespace),
    $data = $videoAdTagData[$field]
    );

    // save bids
    $cache->hSave(
        $hash,
        $field = $videoCacheEventCounter->getCacheKey(VideoCacheEventCounter::CACHE_KEY_BIDS, $namespace),
        $data = $videoAdTagData[$field]
    );

    // save errors
    $cache->hSave(
        $hash,
        $field = $videoCacheEventCounter->getCacheKey(VideoCacheEventCounter::CACHE_KEY_ERRORS, $namespace),
        $data = $videoAdTagData[$field]
    );
}

/**
 * @param VideoCacheEventCounter $videoCacheEventCounter
 * @param ContainerInterface $container
 * @param $namespace
 * @param $videoAdSourceData
 * @param null $originalDate
 */
function saveVideoDemandTagsData(VideoCacheEventCounter $videoCacheEventCounter, ContainerInterface $container, $namespace, $videoAdSourceData, $originalDate = null) {

    $cache = $container->get('tagcade.cache.app_cache');
    $hash = !empty($originalDate) ? getHashFieldDate(VideoCacheEventCounter::REDIS_HASH_VIDEO_EVENT_COUNT, $originalDate) : VideoCacheEventCounter::REDIS_HASH_VIDEO_EVENT_COUNT;
    // save requests
    $cache->hSave(
        $hash,
        $field = $videoCacheEventCounter->getCacheKey(VideoCacheEventCounter::CACHE_KEY_REQUESTS, $namespace),
        $data = $videoAdSourceData[$field]
    );

    // save bids
    $cache->hSave(
        $hash,
        $field = $videoCacheEventCounter->getCacheKey(VideoCacheEventCounter::CACHE_KEY_BIDS, $namespace),
        $data = $videoAdSourceData[$field]
    );

    // save impressions
    $cache->hSave(
        $hash,
        $field = $videoCacheEventCounter->getCacheKey(VideoCacheEventCounter::CACHE_KEY_IMPRESSIONS, $namespace),
        $data = $videoAdSourceData[$field]
    );

    // save clicks
    $cache->hSave(
        $hash,
        $field = $videoCacheEventCounter->getCacheKey(VideoCacheEventCounter::CACHE_KEY_CLICKS, $namespace),
        $data = $videoAdSourceData[$field]
    );

     //save errors
    $cache->hSave(
        $hash,
        $field = $videoCacheEventCounter->getCacheKey(VideoCacheEventCounter::CACHE_KEY_ERRORS, $namespace),
        $data = $videoAdSourceData[$field]
    );

    // save blocks
    $cache->hSave(
        $hash,
        $field = $videoCacheEventCounter->getCacheKey(VideoCacheEventCounter::CACHE_KEY_BLOCKS, $namespace),
        $data = $videoAdSourceData[$field]
    );
}

/**
 * @param $hashField
 * @param DateTime $originalDate
 * @return mixed
 */
function getHashFieldDate($hashField, DateTime $originalDate)
{
    //Build new hash field
    return sprintf("%s:%s", $hashField, $originalDate->format('ymd'));
}
function writeln($str)
{
    echo $str . PHP_EOL;
}