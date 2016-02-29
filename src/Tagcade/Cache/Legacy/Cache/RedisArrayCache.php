<?php

namespace Tagcade\Cache\Legacy\Cache;

// Used for live report data only

use RedisArray;

class RedisArrayCache implements RedisArrayCacheInterface
{
    /**
     * @var RedisArray|\Redis
     */
    private $redis;

    /**
     * Sets the redis array instance to use.
     *
     * @param RedisArray $redis
     *
     * @return void
     */
    public function setRedis(RedisArray $redis)
    {
        $this->redis = $redis;
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