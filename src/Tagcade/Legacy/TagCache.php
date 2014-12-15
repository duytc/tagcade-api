<?php

namespace Tagcade\Legacy;

use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Legacy\Cache\RedisArrayCache;
use Tagcade\Legacy\Cache\Tag\NamespaceCacheInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;

class TagCache implements TagCacheInterface
{
    const NAMESPACE_CACHE_KEY = 'tagcade_adslot_%d';
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

    public function refreshCacheForAdSlot(AdSlotInterface $adSlot)
    {
        $this->cache->setNamespace($this->getNamespace($adSlot->getId()));

        $oldVersion = (int) $this->cache->getNamespaceVersion();
        $newVersion = $oldVersion + 1;

        // create the new version of the cache first
        $this->cache->setNamespaceVersion($newVersion);
        $this->cache->save(static::CACHE_KEY_AD_SLOT, $this->getAdSlotCacheData($adSlot));

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

    public function refreshCache()
    {
        $adSlots = $this->adSlotManager->all();

        foreach ($adSlots as $adSlot) {
            $this->refreshCacheForAdSlot($adSlot);
        }

        return $this;
    }

    protected function getAdSlotCacheData(AdSlotInterface $adSlot)
    {
        $data = [];

        $adTags = $adSlot->getAdTags();

        if (empty($adTags)) {
            return $data;
        }

        $data = array_map(function(AdTagInterface $adTag) {
            return [
                'id'  => $adTag->getId(),
                'tag' => $adTag->getHtml(),
            ];
        }, $adTags->toArray());

        return $data;
    }

    protected function getNamespace($slotId)
    {
        return sprintf(static::NAMESPACE_CACHE_KEY, $slotId);
    }
} 