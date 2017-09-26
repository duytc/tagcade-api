<?php

namespace Tagcade\Cache;

// Used for live report data only

use Redis;

class RedisCache implements RedisCacheInterface
{
    const REDIS_CONNECT_TIMEOUT_IN_SECONDS = 1;

    /**
     * @var Redis
     */
    private $redis;

    private $host;
    private $port;

    /**
     * RedisCache constructor.
     * @param $host
     * @param $port
     */
    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @return Redis|null
     */
    public function getRedis()
    {
        if (!$this->redis instanceof Redis) {
            $redis = new Redis();

            try {
                $redis->connect($this->host, $this->port, self::REDIS_CONNECT_TIMEOUT_IN_SECONDS);
                $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
                // if we don't set this, the read timeout is the same as connect timeout of 1 second
                $redis->setOption(Redis::OPT_READ_TIMEOUT, 5);
                $this->redis = $redis;
            } catch (\RedisException $e) {
                // todo refactor to check if redis is connected or not
                $this->redis = null;
            }
        }

        return $this->redis;
    }

    /**
     * @param mixed $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @param mixed $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    public function multi($host)
    {
        return $this->getRedis()->multi($host);
    }

    public function exec()
    {
        return $this->getRedis()->exec();
    }

    public function hosts()
    {
        return $this->getRedis()->_hosts();
    }

    public function target($key)
    {
        //return $this->redis->_target($key);
        return sprintf('%s:%s', $this->host, $this->port);
    }

    /**
     * {@inheritdoc}
     */
    public function incr($hash)
    {
        return $this->getRedis()->incr($hash);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($id)
    {
        return $this->getRedis()->get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function hFetch($hash, $field)
    {
        return $this->getRedis()->hGet($hash, $field);
    }

    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        return $this->getRedis()->exists($id);
    }

    /**
     * {@inheritdoc}
     */
    public function save($id, $data, $lifeTime = 0)
    {
        if ($lifeTime > 0) {
            return $this->getRedis()->setex($id, $lifeTime, $data);
        }

        return $this->getRedis()->set($id, $data);
    }

    public function hSave($hash, $field, $data)
    {
        return $this->getRedis()->hSet($hash, $field, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->getRedis()->delete($id) > 0;
    }

    public function hDelete($hash, $field)
    {
        return $this->getRedis()->hDel($hash, $field);
    }

    public function mGet(array $keys)
    {
        return $this->getRedis()->mget($keys);
    }

    public function hMGet($key, array $fields)
    {
        return $this->getRedis()->hMGet($key, $fields);
    }

    /**
     * {@inheritdoc}
     */
    public function getStats()
    {
        return null;
    }
}