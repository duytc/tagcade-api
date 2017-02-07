<?php


namespace Tagcade\Service\Report\VideoReport\Counter;


use Tagcade\Cache\Legacy\Cache\RedisCacheInterface;

interface VideoCacheEventCounterInterface extends VideoEventCounterInterface
{
    /**
     * @return RedisCacheInterface cache
     */
    public function getCache();

    /**
     * @param $type
     * @param $id
     * @return string
     */
    public function getCacheKey($type, $id);
}