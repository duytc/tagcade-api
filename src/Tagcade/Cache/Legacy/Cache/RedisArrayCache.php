<?php

namespace Tagcade\Cache\Legacy\Cache;

// Used for live report data only

use Doctrine\Common\Cache\Cache;
use RedisArray;

class RedisArrayCache implements Cache
{
    /**
     * @var RedisArray|null
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
    public function fetch($id)
    {
        return $this->redis->get($id);
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

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->redis->delete($id) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getStats()
    {
        return null;
    }
}