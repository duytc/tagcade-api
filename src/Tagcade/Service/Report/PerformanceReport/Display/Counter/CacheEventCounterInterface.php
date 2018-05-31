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

    /**
     * @param $namespaceFormat
     * @param $id
     * @param null $appendingFormat
     * @param null $appendingId
     * @return mixed
     */
    public function getNamespace($namespaceFormat, $id, $appendingFormat = null, $appendingId = null);

    public function useLocalCache($bool);

    /**
     * @return void
     */
    public function resetLocalCache();
}