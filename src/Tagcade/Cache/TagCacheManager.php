<?php

namespace Tagcade\Cache;


use Tagcade\Cache\V2\TagCache;
use Tagcade\Cache\Legacy\TagCache as LegacyCache;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;

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

    public function refreshCacheForAdSlot(BaseAdSlotInterface $adSlot)
    {
        if ($adSlot instanceof DisplayAdSlotInterface) {
            $this->refreshCacheForDisplayAdSlot($adSlot);
        }
        else if ($adSlot instanceof NativeAdSlotInterface) {
            $this->refreshCacheForNativeAdSlot($adSlot);
        }
        else if ($adSlot instanceof DynamicAdSlotInterface) {
            $this->refreshCacheForDynamicAdSlot($adSlot);
        }
    }


    /**
     * @param DisplayAdSlotInterface $adSlot
     * @param $version
     * @return $this
     */
    public function refreshCacheForDisplayAdSlot(DisplayAdSlotInterface $adSlot, $version = 'All')
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

    public function refreshCacheForRonAdSlot(RonAdSlotInterface $ronAdSlot, $alsoRefreshRelatedDynamicRonAdSlot = true)
    {
        /**
         * @var TagCache[]
         */
        $refreshTagCaches = $this->getTagCachesForVersion(self::VERSION_2);

        foreach ($refreshTagCaches as $tagCache) {
            /**
             * @var TagCache $tagCache
             */
            $tagCache->refreshCacheForRonAdSlot($ronAdSlot);
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