<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Counter;

use Doctrine\Common\Cache\Cache;

interface CacheEventCounterInterface extends EventCounterInterface
{
    /**
     * @return Cache
     */
    public function getCache();

    /**
     * @param $type
     * @param $id
     * @return string
     */
    public function getCacheKey($type, $id);
}