<?php

namespace Tagcade\Cache;


use Tagcade\Cache\Legacy\Cache\Tag\NamespaceCacheInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;

abstract class TagCacheAbstract {

    const CACHE_KEY_AD_SLOT = 'all_tags_array';

    protected $cache;

    public function __construct(NamespaceCacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * refresh Cache For AdSlot
     * @param DisplayAdSlotInterface $adSlot
     * @return $this
     */
    public function refreshCacheForDisplayAdSlot(DisplayAdSlotInterface $adSlot)
    {
        $this->cache->setNamespace($this->getNamespace($adSlot->getId()));

        $oldVersion = (int) $this->cache->getNamespaceVersion();
        $newVersion = $oldVersion + 1;

        // create the new version of the cache first
        $this->cache->setNamespaceVersion($newVersion);
        $this->cache->save(static::CACHE_KEY_AD_SLOT, $this->createAdSlotCacheData($adSlot));

        // delete the old version of the cache
        $this->cache->setNamespaceVersion($oldVersion);

        $this->cache->deleteAll();

        return $this;
    }

    /**
     * @param AdNetworkInterface $adNetwork
     * @return $this
     */
    abstract public function refreshCacheForAdNetwork(AdNetworkInterface $adNetwork);

    /**
     * refresh Cache
     * @return $this
     */
    public abstract function refreshCache();

    public abstract function supportVersion($version);

    protected abstract function createAdSlotCacheData(DisplayAdSlotInterface $adSlot);

    protected abstract function getNamespace($slotId);
}