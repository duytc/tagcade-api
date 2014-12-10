<?php

namespace Tagcade\Legacy\Cache\Tag;

use \Redis;

class RedisNamespacedCache extends NamespaceCacheProvider
{
    /**
     * @var Redis
     */
    protected $redis;

    public function __construct($host = '127.0.0.1', $port = 6379)
    {
        $redis = new Redis();
        $redis->connect($host, $port);

        $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
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
