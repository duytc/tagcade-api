<?php

namespace Tagcade\Cache;


use Tagcade\Cache\DynamicAdSlot\TagCache;
use Tagcade\Cache\Legacy\TagCache as LegacyCache;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;

class TagCacheManager implements TagCacheManagerInterface {

    /**
     * @var TagCacheInterface[]
     */
    private $tagCaches;

    const VERSION_2 = TagCache::VERSION;
    const VERSION_1 = LegacyCache::VERSION;

    function __construct(array $tagCaches)
    {
        foreach($tagCaches as $tagCache) {
            if (!$tagCache instanceof TagCacheInterface) {
                throw new InvalidArgumentException('expect TagCacheInterface');
            }

            $this->tagCaches[] = clone $tagCache;
        }
    }

    /**
     * @param AdSlotInterface $adSlot
     * @param $version
     * @return $this
     */
    public function refreshCacheForDisplayAdSlot(AdSlotInterface $adSlot, $version = 'All')
    {
        $refreshTagCaches = $this->getTagCachesForVersion($version);

        foreach($refreshTagCaches as $tagCache) {
            $tagCache->refreshCacheForDisplayAdSlot($adSlot);
        }

    }



    /**
     * @param AdNetworkInterface $adNetwork
     * @param $version
     * @return $this
     */
    public function refreshCacheForAdNetwork(AdNetworkInterface $adNetwork, $version = 'All')
    {
        $refreshTagCaches = $this->getTagCachesForVersion($version);

        foreach($refreshTagCaches as $tagCache) {
            $tagCache->refreshCacheForAdNetwork($adNetwork);
        }
    }

    public function refreshCacheForDynamicAdSlot(DynamicAdSlotInterface $dynamicAdSlot)
    {
        /**
         * @var TagCache[]
         */
        $refreshTagCaches = $this->getTagCachesForVersion(self::VERSION_2);

        foreach ($refreshTagCaches as $tagCache) {
            /**
             * @var TagCache $tagCache
             */
            $tagCache->refreshCacheForDynamicAdSlot($dynamicAdSlot);
        }

    }

    public function refreshCacheForNativeAdSlot(NativeAdSlotInterface $nativeAdSlot)
    {
        /**
         * @var TagCache[]
         */
        $refreshTagCaches = $this->getTagCachesForVersion(self::VERSION_2);

        foreach ($refreshTagCaches as $tagCache) {
            /**
             * @var TagCache $tagCache
             */
            $tagCache->refreshCacheForNativeAdSlot($nativeAdSlot);
        }
    }


    /**
     * @param $version
     * @return $this
     */
    public function refreshCache($version = 'All')
    {
        $refreshTagCaches = $this->getTagCachesForVersion($version);

        foreach ($refreshTagCaches as $tagCache) {
            $tagCache->refreshCache();
        }
    }


    protected function getTagCachesForVersion($version = 'All') {

        if ('All' === $version) {
            return $this->tagCaches;
        }

        $affectedTagCaches = [];
        foreach ($this->tagCaches as $tagCache) {
            if ($tagCache->supportVersion($version)) {
                $affectedTagCaches[] = $tagCache;
            }
        }

        return $affectedTagCaches;
    }


}