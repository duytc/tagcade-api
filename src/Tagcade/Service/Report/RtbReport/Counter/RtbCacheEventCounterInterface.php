<?php

namespace Tagcade\Service\Report\RtbReport\Counter;

use Tagcade\Cache\Legacy\Cache\RedisArrayCacheInterface;

interface RtbCacheEventCounterInterface extends RtbEventCounterInterface
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