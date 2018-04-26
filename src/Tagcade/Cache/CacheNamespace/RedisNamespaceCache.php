<?php

namespace Tagcade\Cache\CacheNamespace;

use Redis;

class RedisNamespaceCache extends NamespaceCacheProvider
{
    const REDIS_CONNECT_TIMEOUT_IN_SECONDS = 1;

    /**
     * @var Redis
     */
    private $redis;

    private $host;

    private $port;

    function __construct($host, $port, $maxCacheVersion)
    {
        parent::__construct($maxCacheVersion);

        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @param bool $forceFromCache
     * @return bool|int|string
     */
    public function getNamespaceVersion($forceFromCache = false)
    {
        if (false === $forceFromCache && null !== $this->namespaceVersion) {
            return $this->namespaceVersion;
        }

        $namespaceCacheKey = sprintf(NamespaceCacheProvider::NAMESPACE_CACHEKEY, $this->getNamespace());
        $version = $this->doFetch($namespaceCacheKey);

        $version = ($version == false) ? 0 : $version;
        $this->namespaceVersion = $version;

        return $version;
    }

    /**
     * @param $id
     * @param $data
     * @param int $lifetime
     */
    public function saveDataAndIncreaseVersion($id, $data, $lifetime = 0)
    {
        $namespaceVersionKey = $this->getNamespaceVersionKey($this->getNamespace());
        $oldVersion = $this->getNamespaceVersion($forceFromCache = true);
        $newVersion = $oldVersion + 1;
        $this->setNamespaceVersion(($newVersion));

        $this->save($id, $data, $lifetime);
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
     * @return Redis Redis
     */
    public function getRedis()
    {
        if (!$this->redis instanceof Redis) {
            $redis = new Redis();

            try {
                $redis->connect($this->host, $this->port, self::REDIS_CONNECT_TIMEOUT_IN_SECONDS);
                $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
                $this->redis = $redis;
            } catch (\RedisException $e) {
                // todo refactor to check if redis is connected or not
                $this->redis = null;
            }
        }

        return $this->redis;
    }

    /**
     * {@inheritdoc}
     */
    public function doFetch($id)
    {
        return $this->getRedis()->get($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return $this->getRedis()->exists($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doIncrement($id)
    {
        return (bool)$this->getRedis()->incr($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        $result = $this->getRedis()->set($id, $data);

        if ($lifeTime > 0) {
            $this->getRedis()->expire($id, $lifeTime);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        return $this->getRedis()->delete($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        return $this->getRedis()->flushDB();
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        $info = $this->getRedis()->info();

        return array(
            CacheInterface::STATS_HITS => false,
            CacheInterface::STATS_MISSES => false,
            CacheInterface::STATS_UPTIME => $info['uptime_in_seconds'],
            CacheInterface::STATS_MEMORY_USAGE => $info['used_memory'],
            CacheInterface::STATS_MEMORY_AVAILIABLE => false
        );
    }
} 