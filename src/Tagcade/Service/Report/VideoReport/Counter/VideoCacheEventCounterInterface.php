<?php


namespace Tagcade\Service\Report\VideoReport\Counter;


use Tagcade\Cache\Legacy\Cache\RedisArrayCacheInterface;

interface VideoCacheEventCounterInterface extends VideoEventCounterInterface
{
    /**
     * @return RedisArrayCacheInterface cache
     */
    public function getCache();

    /**
     * @param $type
     * @param $id
     * @return string
     */
    public function getCacheKey($type, $id);
}