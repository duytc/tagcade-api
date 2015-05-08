<?php

namespace Tagcade\Cache;


use Tagcade\Cache\Legacy\Cache\Tag\NamespaceCacheInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\DynamicAdSlotManagerInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;

abstract class TagCacheAbstract {

    const CACHE_KEY_AD_SLOT = 'all_tags_array';

    protected $cache;
    /**
     * @var AdSlotManagerInterface
     */
    protected $adSlotManager;

    public function __construct(NamespaceCacheInterface $cache, AdSlotManagerInterface $adSlotManager)
    {
        $this->cache = $cache;
        $this->adSlotManager = $adSlotManager;
    }

    /**
     * refresh Cache For AdSlot
     * @param AdSlotInterface $adSlot
     * @return $this
     */
    public function refreshCacheForAdSlot(AdSlotInterface $adSlot)
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
    public function refreshCacheForAdNetwork(AdNetworkInterface $adNetwork)
    {
        $adTags = $adNetwork->getAdTags();

        $refreshedAdSlots = [];

        foreach ($adTags as $adTag) {
            /**
             * @var AdTagInterface $adTag
             */
            $adSlot = $adTag->getAdSlot();

            if (!in_array($adSlot, $refreshedAdSlots, $strict = true)) {
                $refreshedAdSlots[] = $adSlot;

                $this->refreshCacheForAdSlot($adSlot);
            }

            unset($adSlot, $adTag);
        }
    }

    /**
     * refresh Cache
     * @return $this
     */
    public function refreshCache()
    {
        $adSlots = $this->adSlotManager->all();

        foreach ($adSlots as $adSlot) {
            $this->refreshCacheForAdSlot($adSlot);
        }

        return $this;
    }

    public abstract function supportVersion($version);

    protected abstract function createAdSlotCacheData(AdSlotInterface $adSlot);

    protected abstract function getNamespace($slotId);
}