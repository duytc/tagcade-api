<?php

namespace Tagcade\Cache\Legacy\Cache\Tag;

use \Redis;

class RedisNamespaceCache extends NamespaceCacheProvider
{
    const REDIS_CONNECT_TIMEOUT_IN_SECONDS = 1;

    /**
     * @var Redis
     */
    protected $redis;

    protected $host;

    protected $port;

    public function __construct($maxCacheVersion, $host = '127.0.0.1', $port = 6379)
    {
        parent::__construct($maxCacheVersion);
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Gets the redis instance used by the cache.
     *
     * @return Redis|null
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
    protected function doFetch($id)
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
        return (bool) $this->getRedis()->incr($id);
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
            CacheInterface::STATS_HITS   => false,
            CacheInterface::STATS_MISSES => false,
            CacheInterface::STATS_UPTIME => $info['uptime_in_seconds'],
            CacheInterface::STATS_MEMORY_USAGE       => $info['used_memory'],
            CacheInterface::STATS_MEMORY_AVAILIABLE  => false
        );
    }
}
