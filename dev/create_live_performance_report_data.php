<?php

use Symfony\Component\DependencyInjection\ContainerInterface;

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();

/** @var ContainerInterface $container */
$container = $kernel->getContainer();

$adSlotManager = $container->get('tagcade.domain_manager.ad_slot');
$adTagManager = $container->get('tagcade.domain_manager.ad_tag');
$allAdSLot = $adSlotManager->all();

/*
 * allow do for special publishers,
 * - set [2,3,...] where 2,3 are publisher ids
 * - or set empty for all
 */
$allowedPublishers = [];

if (!empty($allowedPublishers)) {
    $allAdSLot = array_filter($allAdSLot, function ($adSlot) use ($allowedPublishers) {
        return in_array($adSlot->getSite()->getPublisherId(), $allowedPublishers);
    });
}

$allAdSLotMap = [];
/** @var \Tagcade\Model\Core\BaseAdSlotInterface[] $allAdSLot */
foreach ($allAdSLot as $adSlot) {
    $allAdSLotMap[$adSlot->getId()] = $adSlot;
}

writeln('### Start creating test live data for performance reports ###');

$testEventCounter = new \Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter($allAdSLot);
$testEventCounter->refreshTestData();

$cache = $container->get('tagcade.cache.app_cache');
// set no serializer to make sure value is same as come from event processor module, where value is not serialized
// e.g get opportunities:adtag_12:170803 > "3603" instead of "i:3603;"
$cache->getRedis()->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);

$cacheEventCounter = new \Tagcade\Service\Report\PerformanceReport\Display\Counter\CacheEventCounter($cache,
    $adTagManager,
    $adSlotManager,
    $container->getParameter('tc.report.performance.event_counter.redis_pipeline_size_threshold')
);

$cacheEventCounter->setDataWithDateHour(false);
// set date again to make sure that can get right dateFormatter based on $dataWithDateHour
$cacheEventCounter->setDate(new DateTime('now'));

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
$originalDate = $cacheEventCounter->getDate();
// full day
writeln('### creating test live data full day for all ad slots ###');
// set date again to make sure that can get right dateFormatter based on $dataWithDateHour
$cacheEventCounter->setDate($originalDate);
foreach($testEventCounter->getAdSlotData() as $slotId => $slotData) {
    $adSlot = findAdSlot($allAdSLotMap, $slotId);
    saveAdSlotData($cacheEventCounter, $slotId, $slotData, $allAdSLotMap, $container, $testEventCounter);
}

// hourly
writeln('### creating test live data foreach hour for all ad slots ###');
$cacheEventCounter->setDataWithDateHour(true);
foreach($testEventCounter->getAdSlotDataHourly() as $slotId => $slotDataHourly) {
    foreach ($slotDataHourly as $hour => $slotDataHourlyItem) {
        $dataWithDateHour = clone $originalDate;
        if (!$dataWithDateHour instanceof DateTime){
            continue;
        }

        // set hour
        $dataWithDateHour->setTime($hour, 0);
        $cacheEventCounter->setDate($dataWithDateHour);
        saveAdSlotData($cacheEventCounter, $slotId, $slotDataHourlyItem, $allAdSLotMap, $container, $testEventCounter, $originalDate);
    }
}

$cacheEventCounter->setDataWithDateHour(false);
// set date again to make sure that can get right dateFormatter based on $dataWithDateHour
$cacheEventCounter->setDate($originalDate);

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

$originalDate = $cacheEventCounter->getDate();

// full day
writeln('### creating test live data full day for all ad tags ###');
$cacheEventCounter->setDataWithDateHour(false);
// set date again to make sure that can get right dateFormatter based on $dataWithDateHour
$cacheEventCounter->setDate($originalDate);
foreach($testEventCounter->getAdTagData() as $tagId => $tagData) {
    saveAdTagData($cacheEventCounter, $tagId, $tagData, $container, $testEventCounter);
}

// hourly
writeln('### creating test live data day with hour for all ad tags ###');
$cacheEventCounter->setDataWithDateHour(true);
foreach($testEventCounter->getAdTagDataHourly() as $tagId => $tagDataHourly) {
    foreach ($tagDataHourly as $hour => $tagDataHourlyItem) {
        $dataWithDateHour = clone $originalDate;
        if (!$dataWithDateHour instanceof DateTime){
            continue;
        }

        // set hour
        $dataWithDateHour->setTime($hour, 0);
        $cacheEventCounter->setDate($dataWithDateHour);
        saveAdTagData($cacheEventCounter, $tagId, $tagDataHourlyItem, $container, $testEventCounter, $originalDate);
    }
}

$cacheEventCounter->setDataWithDateHour(false);
// set date again to make sure that can get right dateFormatter based on $dataWithDateHour
$cacheEventCounter->setDate($originalDate);

// create and save Dashboard Hourly for account and platform
// this will save much time when getting day-over-day for dashboard
// the command "tc:report:snapshot-by-hour" is already created for production
/** @var \Tagcade\Service\Statistics\Statistics */
$statistics = $container->get('tagcade.service.statistics');
$publisherManager = $container->get('tagcade_user.domain_manager.publisher');
$publishers = $publisherManager->allActivePublishers();
/** @var \Tagcade\Service\Statistics\Util\AccountReportCacheInterface */
$accountReportCache = $container->get('tagcade.service.statistics.util.account_report_cache');
savePublisherDashboardHourlyToRedis($statistics, $originalDate, $publishers, $accountReportCache);
savePlatformDashboardHourlyToRedis($statistics, $originalDate, $accountReportCache);

/**
 * @param \Tagcade\Service\Report\PerformanceReport\Display\Counter\CacheEventCounter $cacheEventCounter
 * @param $tagId
 * @param $tagData
 * @param ContainerInterface $container
 * @param \Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter$testEventCounter
 */
function saveAdTagData (\Tagcade\Service\Report\PerformanceReport\Display\Counter\CacheEventCounter $cacheEventCounter, $tagId, $tagData, ContainerInterface $container, \Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter $testEventCounter, $originalDate = null) {
    $cache = $container->get('tagcade.cache.app_cache');
    $namespace = $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_AD_TAG, $tagId);
    $hash = !empty($originalDate) ? getHashFieldDate($cacheEventCounter::REDIS_HASH_IN_BANNER_EVENT_COUNT, $originalDate) : $cacheEventCounter::REDIS_HASH_IN_BANNER_EVENT_COUNT;

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

    $cache->save(
        $cacheEventCounter->getCacheKey($cacheEventCounter::CACHE_KEY_REFRESHES, $namespace),
        $tagData[$testEventCounter::KEY_REFRESHES]
    );

    $slotId = $testEventCounter->getSlotIdForTag($tagId);
    if ($slotId) {
        $namespace = $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_AD_SLOT, $slotId, $cacheEventCounter::NAMESPACE_AD_TAG, $tagId);
        if (array_key_exists($testEventCounter::KEY_IN_BANNER_IMPRESSIONS, $tagData)) {
            $cache->hSave(
                $hash,
                $cacheEventCounter->getCacheKey(
                    $cacheEventCounter::CACHE_KEY_IN_BANNER_IMPRESSION,
                    $namespace
                ),
                $tagData[$testEventCounter::KEY_IN_BANNER_IMPRESSIONS]
            );
        }

        if (array_key_exists($testEventCounter::KEY_IN_BANNER_REQUESTS, $tagData)) {
            $cache->hSave(
                $hash,
                $cacheEventCounter->getCacheKey(
                    $cacheEventCounter::CACHE_KEY_IN_BANNER_REQUEST,
                    $namespace
                ),
                $tagData[$testEventCounter::KEY_IN_BANNER_REQUESTS]
            );
        }

        if (array_key_exists($testEventCounter::KEY_IN_BANNER_TIMEOUT, $tagData)) {
            $cache->hSave(
                $hash,
                $cacheEventCounter->getCacheKey(
                    $cacheEventCounter::CACHE_KEY_IN_BANNER_TIMEOUT,
                    $namespace
                ),
                $tagData[$testEventCounter::KEY_IN_BANNER_TIMEOUT]
            );
        }
    }

    unset($tagId, $tagData);
}

/**
 * @param \Tagcade\Service\Report\PerformanceReport\Display\Counter\CacheEventCounter $cacheEventCounter
 * @param $slotId
 * @param $slotData
 * @param $allAdSLotMap
 * @param ContainerInterface $container
 * @param \Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter $testEventCounter
 */
function saveAdSlotData (\Tagcade\Service\Report\PerformanceReport\Display\Counter\CacheEventCounter $cacheEventCounter, $slotId, $slotData, $allAdSLotMap, ContainerInterface $container, \Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter $testEventCounter, $originalDate = null) {
    $cache = $container->get('tagcade.cache.app_cache');
    $adSlot = findAdSlot($allAdSLotMap, $slotId);
    $hash = !empty($originalDate) ? getHashFieldDate($cacheEventCounter::REDIS_HASH_IN_BANNER_EVENT_COUNT, $originalDate) : $cacheEventCounter::REDIS_HASH_IN_BANNER_EVENT_COUNT;

    if (array_key_exists($testEventCounter::KEY_SLOT_OPPORTUNITY, $slotData)) {
        $cache->save(
            $cacheEventCounter->getCacheKey(
                $cacheEventCounter::CACHE_KEY_SLOT_OPPORTUNITY,
                $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_AD_SLOT, $slotId)
            ),
            $slotData[$testEventCounter::KEY_SLOT_OPPORTUNITY]
        );
    }

    if (array_key_exists($testEventCounter::KEY_SLOT_OPPORTUNITY_REFRESHES, $slotData)) {
        $cache->save(
            $cacheEventCounter->getCacheKey(
                $cacheEventCounter::CACHE_KEY_SLOT_OPPORTUNITY_REFRESHES,
                $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_AD_SLOT, $slotId)
            ),
            $slotData[$testEventCounter::KEY_SLOT_OPPORTUNITY_REFRESHES]
        );
    }

    if($adSlot->getSite()->getPublisher()->hasInBannerModule()) {
        if (array_key_exists($testEventCounter::KEY_IN_BANNER_REQUESTS, $slotData)) {
            $cache->hSave(
                $hash,
                $cacheEventCounter->getCacheKey(
                    $cacheEventCounter::CACHE_KEY_IN_BANNER_REQUEST,
                    $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_AD_SLOT, $slotId)
                ),
                $slotData[$testEventCounter::KEY_IN_BANNER_REQUESTS]
            );
        }

        if (array_key_exists($testEventCounter::KEY_IN_BANNER_IMPRESSIONS, $slotData)) {
            $cache->hSave(
                $hash,
                $cacheEventCounter->getCacheKey(
                    $cacheEventCounter::CACHE_KEY_IN_BANNER_IMPRESSION,
                    $cacheEventCounter->getNamespace($cacheEventCounter::NAMESPACE_AD_SLOT, $slotId)
                ),
                $slotData[$testEventCounter::KEY_IN_BANNER_IMPRESSIONS]
            );
        }

        if (array_key_exists($testEventCounter::KEY_IN_BANNER_TIMEOUT, $slotData)) {
            $cache->hSave(
                $hash,
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

    unset($tagId, $tagData);
}

/**
 * @param \Tagcade\Service\Statistics\Statistics $statistics
 * @param \DateTime $date
 * @param array|\Tagcade\Model\User\Role\PublisherInterface[] $publishers
 * @param \Tagcade\Service\Statistics\Util\AccountReportCacheInterface $accountReportCache
 */
function savePublisherDashboardHourlyToRedis(\Tagcade\Service\Statistics\Statistics $statistics, \DateTime $date, array $publishers, \Tagcade\Service\Statistics\Util\AccountReportCacheInterface $accountReportCache)
{
    foreach ($publishers as $publisher) {
        if (!$publisher instanceof \Tagcade\Model\User\Role\PublisherInterface) {
            continue;
        }

        $accountReports = $statistics->getPublisherDashboardHourly($publisher, $date, $force = true);
        $accountReportCache->saveHourReports($accountReports);
        writeln(sprintf('Successfully save publisher dashboard hourly to redis (ID: %s)', $publisher->getId()));

        // also save publisher dashboard statistic snapshot to redis
        $accountReports = $statistics->getPublisherDashboard($publisher, $date, $date);
        $accountReportCache->saveCurrentStatisticReports($accountReports);
        writeln(sprintf('Successfully save publisher dashboard statistic snapshot to redis (ID: %s)', $publisher->getId()));
    }
}

/**
 * @param \Tagcade\Service\Statistics\Statistics $statistics
 * @param \DateTime $date
 * @param \Tagcade\Service\Statistics\Util\AccountReportCacheInterface $accountReportCache
 */
function savePlatformDashboardHourlyToRedis(\Tagcade\Service\Statistics\Statistics $statistics, \DateTime $date, \Tagcade\Service\Statistics\Util\AccountReportCacheInterface $accountReportCache)
{
    $platformReports = $statistics->getAdminDashboardHourly($date, $force = true);
    $accountReportCache->saveHourReports($platformReports);
    writeln(sprintf("Successfully save platform dashboard hourly to redis"));

    // also save platform dashboard statistic snapshot to redis
    $platformReports = $statistics->getAdminDashboard($date, $date);
    $accountReportCache->saveCurrentStatisticReports($platformReports);
    writeln(sprintf("Successfully save platform dashboard statistic snapshot to redis"));
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