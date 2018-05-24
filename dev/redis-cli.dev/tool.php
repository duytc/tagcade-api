<?php

define('DEBUG', false);

$redisConfig = [
    'host' => 'tagcade.dev',
    'port' => 6379,
    'timeout' => 3
];

define('QUERY_PARAM_TYPE', 'type');
define('QUERY_PARAM_CACHE_KEY', 'cachekey');
define('QUERY_PARAM_AD_SLOT_ID', 'adSlotId');
define('QUERY_PARAM_AD_SLOT_VERSION', 'adSlotVersion');
define('QUERY_PARAM_WATERFALL_TAG_ID', 'waterfallTagId');
define('QUERY_PARAM_WATERFALL_TAG_VERSION', 'waterfallTagVersion');

define('SUPPORTED_QUERY_PARAMS', [
    QUERY_PARAM_TYPE,
    QUERY_PARAM_CACHE_KEY
]);

define('TYPE_DEFAULT', 'default');
define('TYPE_DISPLAY', 'display');
define('TYPE_VIDEO', 'video');

define('SUPPORTED_TYPE', [
    TYPE_DEFAULT,
    TYPE_DISPLAY,
    TYPE_VIDEO
]);


/**
 * check if current called client is CommandLineInterface
 * @return bool
 */
function isCommandLineInterface()
{
    return (php_sapi_name() === 'cli');
}

/**
 * log, support newLine
 * @param String $msg
 * @param bool $newLine default true
 */
function logConsole($msg, $newLine = true)
{
    $CRLF = isCommandLineInterface() ? "\n" : "<br/>";

    echo $msg . ($newLine ? $CRLF : "");
}

/**
 * connect To Redis Server
 *
 * @param $redisConfig
 * @return bool|Redis
 */
function connectToRedisServer($redisConfig)
{
    $host = $redisConfig['host'];
    $port = $redisConfig['port'];
    $timeout = $redisConfig['timeout'];

    try {
        DEBUG && logConsole('Connecting to Redis server ' . $host . ':' . $port . '...');
        $redis = new Redis();

        if (false === $redis->connect($host, $port, $timeout)) {
            return false;
        }

        $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);

        DEBUG && logConsole('Redis server connected');
        return $redis;
    } catch (RedisException $e) {
        DEBUG && logConsole('Redis server connecting failed');
        return false;
    }
}

// 1. get cacheKey from params
$type = null;
$cacheKey = null;

if (isCommandLineInterface()) {
    if (count($argv) < 3) {
        DEBUG && logConsole('Missing type or cache key params (string)');
        return;
    }

    $type = $argv[1];

    // default
    $cacheKey = $argv[2];

    // display
    $adSlotId = $argv[2];
    $adSlotVersion = $argv[3];

    // display
    $waterfallTagId = $argv[2];
    $waterfallTagVersion = $argv[3];
} else {
    // currently invoked by http call
    header('Content-Type: application/json');

    // todo: can use 'isset($_SERVER['REQUEST_METHOD'])' if need more exactly
    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        DEBUG && logConsole('Not supported method ' . $_SERVER['REQUEST_METHOD']);
        return;
    }

    $queryString = $_SERVER['QUERY_STRING'];

    parse_str($queryString, $queryParams);

    if (!array_key_exists(QUERY_PARAM_TYPE, $queryParams)) {
        logConsole('Missing type param (?type=default|display|video)');
        return;
    }

    $type = $queryParams[QUERY_PARAM_TYPE];
    DEBUG && logConsole('type: ' . $cacheKey);

    // default
    if ($type == TYPE_DEFAULT) {
        if (!array_key_exists(QUERY_PARAM_CACHE_KEY, $queryParams)) {
            logConsole('Missing cachekey param (&cachekey=string)');
            return;
        }

        DEBUG && logConsole('queryParams: ' . json_encode($queryParams, JSON_PRETTY_PRINT));

        $cacheKey = $queryParams[QUERY_PARAM_CACHE_KEY];
        DEBUG && logConsole('cache key: ' . $cacheKey);
    }

    // display
    if ($type == TYPE_DISPLAY) {
        if (!array_key_exists(QUERY_PARAM_AD_SLOT_ID, $queryParams)) {
            logConsole('Missing adSlotId param (&adSlotId=number[&adSlotVersion=number|-1])');
            return;
        }

        DEBUG && logConsole('queryParams: ' . json_encode($queryParams, JSON_PRETTY_PRINT));

        $adSlotId = $queryParams[QUERY_PARAM_AD_SLOT_ID];
        $adSlotVersion = array_key_exists(QUERY_PARAM_AD_SLOT_VERSION, $queryParams) ? (int)$queryParams[QUERY_PARAM_AD_SLOT_VERSION] : -1;
        DEBUG && logConsole('adSlotId: ' . $adSlotId . ', adSlotVersion: ' . $adSlotVersion);
    }

    // video
    if ($type == TYPE_VIDEO) {
        if (!array_key_exists(QUERY_PARAM_WATERFALL_TAG_ID, $queryParams)) {
            logConsole('Missing waterfallTagId param (&waterfallTagId=number[&waterfallTagVersion=number|-1])');
            return;
        }

        DEBUG && logConsole('queryParams: ' . json_encode($queryParams, JSON_PRETTY_PRINT));

        $waterfallTagId = $queryParams[QUERY_PARAM_WATERFALL_TAG_ID];
        $waterfallTagVersion = array_key_exists(QUERY_PARAM_WATERFALL_TAG_VERSION, $queryParams) ? (int)$queryParams[QUERY_PARAM_WATERFALL_TAG_VERSION] : -1;
        DEBUG && logConsole('waterfallTagId: ' . $waterfallTagId . ', waterfallTagVersion: ' . $waterfallTagVersion);
    }
}

/* 2. connect to redis */
$redis = connectToRedisServer($redisConfig);

if (!$redis) {
    return;
}

/* 3. get data */
function getDisplayCacheVersion(Redis $redis, $adSlotId)
{
    $versionKey = sprintf('TagcadeNamespaceCacheKey[tagcade_adslot_v2_%d]', $adSlotId);

    return $redis->get($versionKey);
}


function getDisplayCacheConfig(Redis $redis, $adSlotId, $adSlotVersion)
{
    $version = ($adSlotVersion == -1) ? getDisplayCacheVersion($redis, $adSlotId) : $adSlotVersion;

    if (!$version) {
        return false;
    }

    $cacheKey = sprintf('tagcade_adslot_v2_%d[all_tags_array][%d]', $adSlotId, $version);

    return [
        'SLOT_ID' => $adSlotId,
        'VERSION' => $version,
        'CACHE' => $redis->get($cacheKey)
    ];
}

function getVideoCacheVersion(Redis $redis, $waterfallTagId)
{
    $versionKey = sprintf('TagcadeNamespaceCacheKey[video:waterfall_tag:%s:tag_config]', $waterfallTagId);

    return $redis->get($versionKey);
}

function getVideoCacheConfig(Redis $redis, $waterfallTagId, $waterfallTagVersion)
{
    $version = ($waterfallTagVersion == -1) ? getVideoCacheVersion($redis, $waterfallTagId) : $waterfallTagVersion;

    if (!$version) {
        return false;
    }

    $cacheKey = sprintf('video:waterfall_tag:%s:tag_config[all_demand_ad_tags_array][%d]', $waterfallTagId, $version);

    return [
        'TAG_UUID' => $waterfallTagId,
        'VERSION' => $version,
        'CACHE' => $redis->get($cacheKey)
    ];
}

$cacheData = false;

if (TYPE_DEFAULT == $type) {
    $cacheData = $redis->get($cacheKey);

    if (!$redis->exists($cacheKey)) {
        logConsole('Cache key ' . $cacheKey . ' does not existed');
        return;
    }
}

if (TYPE_DISPLAY == $type) {
    $cacheData = getDisplayCacheConfig($redis, $adSlotId, $adSlotVersion);
}

if (TYPE_VIDEO == $type) {
    $cacheData = getVideoCacheConfig($redis, $waterfallTagId, $waterfallTagVersion);
}

if (!$cacheData) {
    logConsole('Could not get Cache data for cache key ' . $cacheKey);
    return;
}

/* 4. pretty print */
$cacheDataJson = json_encode($cacheData, JSON_PRETTY_PRINT);

if (json_last_error() != JSON_ERROR_NONE) {
    return;
}


DEBUG && logConsole('Cache data: ');
logConsole($cacheDataJson, $newLine = false);