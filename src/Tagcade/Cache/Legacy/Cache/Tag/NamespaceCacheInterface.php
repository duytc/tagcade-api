<?php
/*
 * Based on doctrine cache
 */

namespace Tagcade\Cache\Legacy\Cache\Tag;

interface NamespaceCacheInterface
{
    public function setNamespace($namespace);

    /**
     * Retrieve the namespace that prefixes all cache ids.
     *
     * @return string
     */
    public function getNamespace();

    public function setNamespaceVersion($version);

    /**
     * Namespace version
     *
     * @return string $namespaceVersion
     */
    public function getNamespaceVersion($forceFromCache=false);

    /**
     * Fetches an entry from the cache.
     *
     * @param string $id cache id The id of the cache entry to fetch.
     * @return mixed The cached data or FALSE, if no cache entry exists for the given id.
     */
    function fetch($id);

    /**
     * Test if an entry exists in the cache.
     *
     * @param string $id cache id The cache id of the entry to check for.
     * @return boolean TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    function contains($id);

    /**
     * Increase key value
     *
     * @param string $id cache id The cache id of the entry to check for.
     * @return boolean TRUE if value is incremented, FALSE otherwise.
     */
    function increment($id);

    /**
     * Puts data into the cache.
     *
     * @param string $id The cache id.
     * @param mixed $data The cache entry/data.
     * @param int $lifeTime The lifetime. If != 0, sets a specific lifetime for this cache entry (0 => infinite lifeTime).
     * @return boolean TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    function save($id, $data, $lifeTime = 0);

    /**
     * Deletes a cache entry.
     *
     * @param string $id cache id
     * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    function delete($id);

    /**
     * Delete all cache entries.
     *
     * @return boolean TRUE if the cache entries were successfully deleted, FALSE otherwise.
     */
    public function deleteAll();
}
