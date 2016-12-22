<?php

namespace Tagcade\Cache\Legacy\Cache\Tag;

use \Redis;

class RedisNamespacedCache extends NamespaceCacheProvider
{
    const REDIS_CONNECT_TIMEOUT_IN_SECONDS = 1;

    /**
     * @var Redis
     */
    protected $redis;

    public function __construct($maxCacheVersion, $host = '127.0.0.1', $port = 6379)
    {
        parent::__construct($maxCacheVersion);
        $redis = new Redis();

        try {
            $redis->connect($host, $port, self::REDIS_CONNECT_TIMEOUT_IN_SECONDS);
            $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
        } catch (\RedisException $e) {
            // do not let redis connection errors crash the entire program
            // todo refactor to check if redis is connected or not
        }

        $this->redis = $redis;
    }

    /**
     * Gets the redis instance used by the cache.
     *
     * @return Redis
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
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
