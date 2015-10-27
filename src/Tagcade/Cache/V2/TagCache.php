<?php

namespace Tagcade\Cache\V2;

use Doctrine\ORM\PersistentCollection;
use Tagcade\Cache\V2\Behavior\CreateAdSlotDataTrait;
use Tagcade\Cache\Legacy\Cache\Tag\NamespaceCacheInterface;
use Tagcade\Cache\TagCacheAbstract;
use Tagcade\Cache\TagCacheInterface;
use Tagcade\Cache\V2\Refresher\AdSlotCacheInterface;
use Tagcade\Cache\V2\Refresher\RonAdSlotCacheInterface;
use Tagcade\DomainManager\DisplayAdSlotManagerInterface;
use Tagcade\DomainManager\DynamicAdSlotManagerInterface;
use Tagcade\DomainManager\NativeAdSlotManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Exception\NotSupportedException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;

class TagCache extends TagCacheAbstract implements TagCacheInterface, TagCacheV2Interface
{
    const VERSION = 2;

    /**
     * @var AdSlotCacheInterface
     */
    private $adSlotCache;
    /**
     * @var RonAdSlotCacheInterface
     */
    private $ronAdSlotCache;

    public function __construct(NamespaceCacheInterface $cache, AdSlotCacheInterface $adSlotCache, RonAdSlotCacheInterface $ronAdSlotCache)
    {
        parent::__construct($cache);

        $this->adSlotCache = $adSlotCache;
        $this->ronAdSlotCache = $ronAdSlotCache;
    }

    /**
     * refresh Cache
     * @return $this
     */
    public function refreshCache()
    {
        $this->adSlotCache->refreshCache();

        $this->ronAdSlotCache->refreshCache();

        return $this;
    }

    public function refreshCacheForRonAdSlot(RonAdSlotInterface $ronAdSlot, $alsoRefreshRelatedDynamicRonAdSlot = true)
    {
        return $this->ronAdSlotCache->refreshCacheForRonAdSlot($ronAdSlot, $alsoRefreshRelatedDynamicRonAdSlot);
    }


    /**
     *
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
            if (!$adSlot instanceof ReportableAdSlotInterface) {
                throw new LogicException('Only ReportableAdSlotInterface contains ad tags');
            }

            if (!in_array($adSlot, $refreshedAdSlots, $strict = true)) {
                $refreshedAdSlots[] = $adSlot;
                $this->refreshCacheForReportableAdSlot($adSlot);
            }

            unset($adSlot, $adTag);
        }
    }

    public function refreshCacheForReportableAdSlot(ReportableAdSlotInterface $adSlot, $alsoRefreshRelatedDynamicAdSlot = true)
    {
        if($adSlot instanceof DisplayAdSlotInterface) {
            $this->refreshCacheForDisplayAdSlot($adSlot, $alsoRefreshRelatedDynamicAdSlot);
        }
        else if ($adSlot instanceof NativeAdSlotInterface) {
            $this->refreshCacheForNativeAdSlot($adSlot, $alsoRefreshRelatedDynamicAdSlot);
        }
        else {
            throw new NotSupportedException('Not supported refreshing cache for this type of ad slot yet');
        }
    }

    /**
     * @inheritdoc
     */
    public function refreshCacheForDisplayAdSlot(DisplayAdSlotInterface $adSlot, $alsoRefreshRelatedDynamicAdSlot = true)
    {
       return $this->adSlotCache->refreshCacheForDisplayAdSlot($adSlot, $alsoRefreshRelatedDynamicAdSlot);
    }

    /**
     * refresh cache for DynamicAdSlot
     * @param DynamicAdSlotInterface $dynamicAdSlot
     * @return $this
     */
    public function refreshCacheForDynamicAdSlot(DynamicAdSlotInterface $dynamicAdSlot)
    {
        return $this->adSlotCache->refreshForCacheKey(self::CACHE_KEY_AD_SLOT, $dynamicAdSlot);
    }


    public function refreshCacheForNativeAdSlot(NativeAdSlotInterface $nativeAdSlot,  $alsoRefreshRelatedDynamicAdSlot = true)
    {
        return $this->adSlotCache->refreshCacheForNativeAdSlot($nativeAdSlot, $alsoRefreshRelatedDynamicAdSlot);
    }

    protected function createAdSlotCacheData(DisplayAdSlotInterface $adSlot)
    {
        return $this->adSlotCache->createAdSlotCacheData($adSlot);
    }

    protected function getNamespace($slotId)
    {
        return $this->adSlotCache->getNamespace($slotId);
    }

    public function supportVersion($version)
    {
        return $version === self::VERSION;
    }
}