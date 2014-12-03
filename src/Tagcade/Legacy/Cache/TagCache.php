<?php

namespace Tagcade\Legacy;

use Doctrine\Common\Cache\Cache;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Legacy\Cache\RedisArrayCache;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;

class TagCache implements TagCacheInterface
{
    /**
     * @var AdSlotManagerInterface
     */
    private $adSlotManager;
    /**
     * @var AdTagManagerInterface
     */
    private $adTagManager;
    /**
     * @var NamespaceCacheInterface
     */
    private $namespaceCache;
    /**
     * @var RedisArrayCache
     */
    private $cache;

    function __construct(AdSlotManagerInterface $adSlotManager, AdTagManagerInterface $adTagManager, NamespaceCacheInterface $namespaceCache, RedisArrayCache $cache)
    {
        $this->adSlotManager = $adSlotManager;
        $this->adTagManager = $adTagManager;
        $this->namespaceCache = $namespaceCache;
        $this->cache = $cache;
    }

    public function renewCacheForAdSlot(AdSlotInterface $adSlotId)
    {
        // TODO: renew cache for one ad slot
        // Step 1. Clear previous data

        // Step 2. Generate new cache key
        $newKey = 'all_tags_array'; //all_tags_array
        // Step 3. Store new data with new cache key
        $this->saveCacheAtKey($newKey, $adSlotId);
    }

    public function renewCache()
    {
        $adSlots = $this->adSlotManager->all();

        foreach ($adSlots as $adSlot) {
            $this->renewCacheForAdSlot($adSlot);
        }
    }

    /**
     * @param $cacheKey
     * @param AdSlotInterface $adSlot
     * @return bool
     */
    protected function saveCacheAtKey($cacheKey, AdSlotInterface $adSlot)
    {
        $tagArray = $this->createCacheDataForAdSlot($adSlot);

        return $this->cache->save($cacheKey, $tagArray);
    }

    /**
     * @param AdSlotInterface $adSlot
     * @return array
     */
    protected function createCacheDataForAdSlot(AdSlotInterface $adSlot)
    {
        $adTags = $this->adTagManager->getAdTagsForAdSlot($adSlot);

        $tagArray = [];

        /**
         * @var AdTagInterface $tag
         */

        foreach ($adTags as $tag) {
            $tempTagData = ['id'  => $tag->getId(), 'tag' => $tag->getTag()];

            if (null !== $tag->getBlockSuspiciousTraffic()) {
                $tempTagData['bst'] = (bool) $tag->getBlockSuspiciousTraffic();
            }

            $tagArray[] = $tempTagData;

            unset($tempTagData);
        }

        return $tagArray;
    }

} 