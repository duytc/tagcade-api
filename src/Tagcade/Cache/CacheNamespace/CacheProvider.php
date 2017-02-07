<?php

namespace Tagcade\Cache\CacheNamespace;

abstract class CacheProvider implements CacheInterface
{
    /**
     * {@inheritdoc}
     */
    abstract public function fetch($id);

    /**
     * {@inheritdoc}
     */
    abstract public function contains($id);

    /**
     * {@inheritdoc}
     */
    abstract public function increment($id);

    /**
     * {@inheritdoc}
     */
    abstract public function save($id, $data, $lifeTime = 0);

    /**
     * {@inheritdoc}
     */
    abstract public function delete($id);

    /**
     * {@inheritdoc}
     */
    abstract public function getStats();

    /**
     * Deletes all cache entries.
     *
     * @return boolean TRUE if the cache entries were successfully flushed, FALSE otherwise.
     */
    abstract public function flushAll();
}
