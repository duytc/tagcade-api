<?php

namespace Tagcade\Legacy\Cache;

use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Legacy\Cache\RedisArrayCache;
use Tagcade\Model\Core\AdSlotInterface;

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
     * @var RedisArrayCache
     */
    private $cache;

//    function __construct(AdSlotManagerInterface $adSlotManager, AdTagManagerInterface $adTagManager, RedisArrayCache $cache)
//    {
//        $this->adSlotManager = $adSlotManager;
//        $this->adTagManager = $adTagManager;
//        $this->cache = $cache;
//    }

    public function renewCacheForAdSlot(AdSlotInterface $adSlotId)
    {
        return $this;
//        // TODO: renew cache for one ad slot
//        // Step 1. Clear previous data
//
//        // Step 2. Generate new cache key
//        $newKey = 'all_tags_array'; //all_tags_array
//        // Step 3. Store new data with new cache key
//        $this->saveCacheAtKey($newKey, $adSlotId);
    }

    public function renewCache()
    {
        return $this;
//        $adSlots = $this->adSlotManager->all();
//
//        foreach ($adSlots as $adSlot) {
//            $this->renewCacheForAdSlot($adSlot);
//        }
    }

//    /**
//     * @param $cacheKey
//     * @param AdSlotInterface $adSlot
//     * @return bool
//     */
//    protected function saveCacheAtKey($cacheKey, AdSlotInterface $adSlot)
//    {
//        $tagArray = $this->createCacheDataForAdSlot($adSlot);
//
//        return $this->cache->save($cacheKey, $tagArray);
//    }
//
//    /**
//     * @param AdSlotInterface $adSlot
//     * @return array
//     */
//    protected function createCacheDataForAdSlot(AdSlotInterface $adSlot)
//    {
//        $adTags = $this->adTagManager->getAdTagsForAdSlot($adSlot);
//
//        $tagArray = [];
//
//        /**
//         * @var AdTagInterface $tag
//         */
//
//        foreach ($adTags as $tag) {
//            $tempTagData = ['id'  => $tag->getId(), 'tag' => $tag->getTag()];
//
//            if (null !== $tag->getBlockSuspiciousTraffic()) {
//                $tempTagData['bst'] = (bool) $tag->getBlockSuspiciousTraffic();
//            }
//
//            $tagArray[] = $tempTagData;
//
//            unset($tempTagData);
//        }
//
//        return $tagArray;
//    }

} 