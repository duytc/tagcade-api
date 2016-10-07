<?php

namespace Tagcade\Cache\CacheNamespace;

use Redis;
use RedisArray;
use Tagcade\Cache\Legacy\Cache\Tag\CacheInterface;
use Tagcade\Cache\Legacy\Cache\Tag\NamespaceCacheProvider;

class RedisArrayNamespaceCache extends NamespaceCacheProvider
{

    /**
     * @var RedisArray|\Redis
     */
    private $redis;

    function __construct($redisArray, $maxCacheVersion)
    {
        parent::__construct($maxCacheVersion);

        $ra = new RedisArray($redisArray);
        $this->redis = $ra;
        $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
    }

    /**
     * @param bool $forceFromCache
     * @return bool|int|string
     */
    public function getNamespaceVersion($forceFromCache = false)
    {
        if (false === $forceFromCache) {
            return $this->namespaceVersion;
        }

        $namespaceCacheKey = sprintf(NamespaceCacheProvider::NAMESPACE_CACHEKEY, $this->getNamespace());
        $version = $this->doFetch($namespaceCacheKey);

        $version = ($version == false)? 0 : $version;

        return $version;
    }

    /**
     * @param $id
     * @param $data
     * @param int $lifetime
     */
    public function saveDataAndIncreaseVersion($id, $data, $lifetime =0)
    {
        $namespaceVersionKey = $this->getNamespaceVersionKey($this->getNamespace());
        $oldVersion = $this->getNamespaceVersion(true);
        $newVersion = $oldVersion + 1;
        $this->setNamespaceVersion(($newVersion));

        $this->save($id,$data,$lifetime);
        $this->doSave($namespaceVersionKey, $this->getNamespaceVersion());
    }

    public function removeCache($id)
    {
        $this->delete($id);
    }

    /**
     * @param $namespaceId
     * @return bool
     */
    public function getNamespaceVersionKey($namespaceId)
    {
        $namespaceCacheKey = sprintf(NamespaceCacheProvider::NAMESPACE_CACHEKEY, $namespaceId);

        return $namespaceCacheKey;
    }

    /**
     * @param $version
     */
    public function saveVersion($version)
    {
         $this->setNamespaceVersion($version);
    }

    /**
     * Gets the redis instance used by the cache.
     *
     * @return RedisArray Redis
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * {@inheritdoc}
     */
    public function doFetch($id)
    {
        return $this->redis->get($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return $this->redis->exists($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doIncrement($id)
    {
        return (bool) $this->redis->incr($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        $result = $this->redis->set($id, $data);
        if ($lifeTime > 0) {
            $this->redis->expire($id, $lifeTime);
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        return $this->redis->delete($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        return $this->redis->flushDB();
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        $info = $this->redis->info();
        return array(
            CacheInterface::STATS_HITS   => false,
            CacheInterface::STATS_MISSES => false,
            CacheInterface::STATS_UPTIME => $info['uptime_in_seconds'],
            CacheInterface::STATS_MEMORY_USAGE       => $info['used_memory'],
            CacheInterface::STATS_MEMORY_AVAILIABLE  => false
        );
    }
} 