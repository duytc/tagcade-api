<?php

namespace Tagcade\Cache;


use Tagcade\Cache\Legacy\Cache\Tag\NamespaceCacheInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;

abstract class TagCacheAbstract
{
    const CACHE_KEY_AD_SLOT = 'all_tags_array';
    const CACHE_KEY_CDN_AD_SLOT = 'cdn_all_tags_array';

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

        $oldVersion = (int)$this->cache->getNamespaceVersion();
        $newVersion = $oldVersion + 1;

        // create the new version of the cache first
        $this->cache->setNamespaceVersion($newVersion);
        $this->cache->save(static::CACHE_KEY_AD_SLOT, $this->createAdSlotCacheData($adSlot));

        $this->cache->deleteAll();

        return $this;
    }

    public function removeCacheForAdSlot($adSlotId)
    {
        $this->cache->setNamespace($this->getNamespace($adSlotId));
        $this->cache->removeNamespaceCacheKey();
        $maxVersion = $this->cache->getMaxCacheVersion();
        for ($i = 1; $i <= $maxVersion; $i++) {
            $this->cache->setNamespaceVersion($i);
            $this->cache->delete(static::CACHE_KEY_AD_SLOT);
            $this->cache->delete(static::CACHE_KEY_CDN_AD_SLOT);
        }
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

    /**
     * check if supports Version
     *
     * @param $version
     * @return mixed
     */
    public abstract function supportVersion($version);

    /**
     * create AdSlotCacheData
     *
     * @param DisplayAdSlotInterface $adSlot
     * @return mixed
     */
    protected abstract function createAdSlotCacheData(DisplayAdSlotInterface $adSlot);

    /**
     * get Namespace for an ad slot
     *
     * @param $slotId
     * @return mixed
     */
    protected abstract function getNamespace($slotId);
}