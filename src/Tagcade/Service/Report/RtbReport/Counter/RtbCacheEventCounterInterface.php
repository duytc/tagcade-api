<?php

namespace Tagcade\Service\Report\RtbReport\Counter;

use Tagcade\Cache\Legacy\Cache\RedisCacheInterface;

interface RtbCacheEventCounterInterface extends RtbEventCounterInterface
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