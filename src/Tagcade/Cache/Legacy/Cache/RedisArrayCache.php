<?php

namespace Tagcade\Cache\Legacy\Cache;

// Used for live report data only

use Redis;

class RedisArrayCache implements RedisArrayCacheInterface
{
    /**
     * @var Redis
     */
    private $redis;

    private $host;
    private $port;

    /**
     * Sets the redis array instance to use.
     *
     * @param Redis $redis
     *
     * @return void
     */
    public function setRedis(Redis $redis)
    {
        $this->redis = $redis;
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
        return $this->redis->multi($host);
    }

    public function exec()
    {
        return $this->redis->exec();
    }

    public function hosts()
    {
        return $this->redis->_hosts();
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
        return $this->redis->incr($hash);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($id)
    {
        return $this->redis->get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function hFetch($hash, $field)
    {
        return $this->redis->hGet($hash, $field);
    }

    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        return $this->redis->exists($id);
    }

    /**
     * {@inheritdoc}
     */
    public function save($id, $data, $lifeTime = 0)
    {
        if ($lifeTime > 0) {
            return $this->redis->setex($id, $lifeTime, $data);
        }

        return $this->redis->set($id, $data);
    }

    public function hSave($hash, $field, $data)
    {
        return $this->redis->hSet($hash, $field, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->redis->delete($id) > 0;
    }

    public function hDelete($hash, $field)
    {
        return $this->redis->hDel($hash, $field);
    }

    public function mGet(array $keys)
    {
        return $this->redis->mget($keys);
    }

    public function hMGet($key, array $fields)
    {
        return $this->redis->hMGet($key, $fields);
    }

    /**
     * {@inheritdoc}
     */
    public function getStats()
    {
        return null;
    }
}