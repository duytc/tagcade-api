<?php

namespace Tagcade\Cache\Legacy\Cache\Tag;

abstract class NamespaceCacheProvider extends CacheProvider implements NamespaceCacheInterface
{
    const NAMESPACE_CACHEKEY = 'TagcadeNamespaceCacheKey[%s]';

    /**
     * @var string The namespace to prefix all cache ids with
     */
    private $namespace = '';

    /**
     * @var string The namespace version
     */
    private $namespaceVersion;

    /**
     * Set the namespace to prefix all cache ids with.
     *
     * @param string $namespace
     * @return void
     */
    public function setNamespace($namespace)
    {
        if ($namespace !== $this->namespace) {
            $this->namespace = (string)$namespace;
            $this->namespaceVersion = null;
        }
    }

    /**
     * Retrieve the namespace that prefixes all cache ids.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    public function setNamespaceVersion($version)
    {
        $this->namespaceVersion = (int)$version;
    }

    /**
     * Namespace version
     *
     * @param bool $forceFromCache
     * @return string $namespaceVersion
     */
    public function getNamespaceVersion($forceFromCache = false)
    {
        if (false === $forceFromCache && null !== $this->namespaceVersion) {
            return $this->namespaceVersion;
        }

        $namespaceCacheKey = $this->getNamespaceCacheKey();
        $namespaceVersion = $this->doFetch($namespaceCacheKey);

        if (false === $namespaceVersion) {
            $namespaceVersion = 1;

            $this->doSave($namespaceCacheKey, $namespaceVersion);
        }

        $this->namespaceVersion = $namespaceVersion;

        return $this->namespaceVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($id)
    {
        return $this->doFetch($this->getNamespacedId($id));
    }

    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        return $this->doContains($this->getNamespacedId($id));
    }

    /**
     * {@inheritdoc}
     */
    public function increment($id)
    {
        return $this->doIncrement($this->getNamespacedId($id));
    }

    /**
     * {@inheritdoc}
     */
    public function save($id, $data, $lifeTime = 0)
    {
        return $this->doSave($this->getNamespacedId($id), $data, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->doDelete($this->getNamespacedId($id));
    }

    /**
     * {@inheritdoc}
     */
    public function getStats()
    {
        return $this->doGetStats();
    }

    /**
     * Deletes all cache entries.
     *
     * @return boolean TRUE if the cache entries were successfully flushed, FALSE otherwise.
     */
    public function flushAll()
    {
        return $this->doFlush();
    }

    /**
     * Delete all cache entries.
     *
     * @return boolean TRUE if the cache entries were successfully deleted, FALSE otherwise.
     */
    public function deleteAll()
    {
        $namespaceCacheKey = $this->getNamespaceCacheKey();
        $namespaceVersion = $this->getNamespaceVersion() + 1;

        $this->namespaceVersion = $namespaceVersion;

        return $this->doSave($namespaceCacheKey, $namespaceVersion);
    }

    /**
     * Prefix the passed id with the configured namespace value
     *
     * @param string $id The id to namespace
     * @return string $id The namespaced id
     */
    private function getNamespacedId($id)
    {
        $namespaceVersion = $this->getNamespaceVersion();

        return sprintf('%s[%s][%s]', $this->namespace, $id, $namespaceVersion);
    }

    /**
     * Namespace cache key
     *
     * @return string $namespaceCacheKey
     */
    private function getNamespaceCacheKey()
    {
        return sprintf(self::NAMESPACE_CACHEKEY, $this->namespace);
    }

    /**
     * Fetches an entry from the cache.
     *
     * @param string $id cache id The id of the cache entry to fetch.
     * @return string The cached data or FALSE, if no cache entry exists for the given id.
     */
    abstract protected function doFetch($id);

    /**
     * Test if an entry exists in the cache.
     *
     * @param string $id cache id The cache id of the entry to check for.
     * @return boolean TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    abstract protected function doContains($id);

    /**
     * Increment cache key
     *
     * @param string $id cache id The cache id of the entry to increment.
     * @return boolean TRUE if value is incremented, FALSE otherwise.
     */
    abstract protected function doIncrement($id);

    /**
     * Puts data into the cache.
     *
     * @param string $id The cache id.
     * @param string $data The cache entry/data.
     * @param bool|int $lifeTime The lifetime. If != false, sets a specific lifetime for this
     *                           cache entry (null => infinite lifeTime).
     *
     * @return boolean TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    abstract protected function doSave($id, $data, $lifeTime = false);

    /**
     * Deletes a cache entry.
     *
     * @param string $id cache id
     * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    abstract protected function doDelete($id);

    /**
     * Deletes all cache entries.
     *
     * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    abstract protected function doFlush();

    /**
     * Retrieves cached information from data store
     *
     * @since   2.2
     * @return  array An associative array with server's statistics if available, NULL otherwise.
     */
    abstract protected function doGetStats();
}
