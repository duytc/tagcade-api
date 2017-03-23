<?php

namespace Tagcade\Cache\V2\Refresher;


use Tagcade\Cache\CacheNamespace\NamespaceCacheInterface;
use Tagcade\Cache\V2\Behavior\CreateAdSlotDataTrait;
use Tagcade\Cache\V2\DisplayDomainListManagerInterface;
use Tagcade\DomainManager\DisplayAdSlotManagerInterface;
use Tagcade\DomainManager\DisplayBlacklistManagerInterface;
use Tagcade\DomainManager\DynamicAdSlotManagerInterface;
use Tagcade\DomainManager\NativeAdSlotManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\ExpressionRepositoryInterface;
use Tagcade\Service\TagGenerator;
use Tagcade\Worker\Manager;

class AdSlotCache extends RefresherAbstract implements AdSlotCacheInterface
{
    use CreateAdSlotDataTrait;

    /**
     * @var DynamicAdSlotManagerInterface
     */
    protected $dynamicAdSlotManager;
    /**
     * @var ExpressionRepositoryInterface
     */
    protected $expressionRepository;
    /**
     * @var DisplayAdSlotManagerInterface
     */
    private $displayAdSlotManager;
    /**
     * @var NativeAdSlotManagerInterface
     */
    private $nativeAdSlotManager;
    /**
     * @var TagGenerator
     */
    private $tagGenerator;

    /**
     * @var DisplayBlacklistManagerInterface $displayBlacklistManager
     */
    protected $displayBlacklistManager;

    /**
     * @var string
     */
    protected $blacklistPrefix;

    public function __construct(NamespaceCacheInterface $cache,
                                Manager $workerManager,
                                DisplayAdSlotManagerInterface $displayAdSlotManager,
                                NativeAdSlotManagerInterface $nativeAdSlotManager,
                                DynamicAdSlotManagerInterface $dynamicAdSlotManager,
                                ExpressionRepositoryInterface $expressionRepository,
                                TagGenerator $tagGenerator,
                                DisplayBlacklistManagerInterface $displayBlacklistManager,
                                $blacklistPrefix)
    {
        parent::__construct($cache, $workerManager);

        $this->expressionRepository = $expressionRepository;
        $this->dynamicAdSlotManager = $dynamicAdSlotManager;
        $this->displayAdSlotManager = $displayAdSlotManager;
        $this->nativeAdSlotManager = $nativeAdSlotManager;
        $this->tagGenerator = $tagGenerator;
        $this->displayBlacklistManager = $displayBlacklistManager;
        $this->blacklistPrefix = $blacklistPrefix;
    }

    /**
     * @inheritdoc
     */
    public function refreshCache($publisher = null)
    {
        /** @var DisplayAdSlotInterface[] $adSlots */
        $adSlots = ($publisher instanceof PublisherInterface)
            ? $this->displayAdSlotManager->getAdSlotsForPublisher($publisher)
            : $this->displayAdSlotManager->all();
        foreach ($adSlots as $adSlot) {
            $this->refreshCacheForDisplayAdSlot($adSlot, false);
        }

        /** @var NativeAdSlotInterface[] $nativeAdSlots */
        $nativeAdSlots = ($publisher instanceof PublisherInterface)
            ? $this->nativeAdSlotManager->getNativeAdSlotsForPublisher($publisher)
            : $this->nativeAdSlotManager->all();
        foreach ($nativeAdSlots as $nativeAdSlot) {
            $this->refreshCacheForNativeAdSlot($nativeAdSlot, false);
        }

        /** @var DynamicAdSlotInterface[] $dynamicAdSlots */
        $dynamicAdSlots = ($publisher instanceof PublisherInterface)
            ? $this->dynamicAdSlotManager->getDynamicAdSlotsForPublisher($publisher)
            : $this->dynamicAdSlotManager->all();
        foreach ($dynamicAdSlots as $dynamicAdSlot) {
            $this->refreshCacheForDynamicAdSlot($dynamicAdSlot);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function refreshCacheForDisplayAdSlot(DisplayAdSlotInterface $adSlot, $alsoRefreshRelatedDynamicAdSlot = true)
    {
        //step 1. refresh cache for AdSlot
        $this->refreshForCacheKey(self::CACHE_KEY_AD_SLOT, $adSlot);

        if (!$alsoRefreshRelatedDynamicAdSlot) {
            return $this;
        }

        //step 2. refresh cache for all affected DynamicAdSlots
        return $this->refreshCacheForReferencingDynamicAdSlot($adSlot);
    }

    /**
     * @inheritdoc
     */
    public function refreshCacheForNativeAdSlot(NativeAdSlotInterface $nativeAdSlot, $alsoRefreshRelatedDynamicAdSlot = true)
    {
        $this->refreshForCacheKey(self::CACHE_KEY_AD_SLOT, $nativeAdSlot);

        if (!$alsoRefreshRelatedDynamicAdSlot) {
            return $this;
        }

        //step 2. refresh cache for all affected DynamicAdSlots
        return $this->refreshCacheForReferencingDynamicAdSlot($nativeAdSlot);
    }

    /**
     * refresh cache for DynamicAdSlot
     * @param DynamicAdSlotInterface $dynamicAdSlot
     * @return $this
     */
    public function refreshCacheForDynamicAdSlot(DynamicAdSlotInterface $dynamicAdSlot)
    {
        return $this->refreshForCacheKey(self::CACHE_KEY_AD_SLOT, $dynamicAdSlot);
    }


    /**
     * @inheritdoc
     */
    public function refreshForCacheKey($cacheKey, ModelInterface $model)
    {
        if ($cacheKey !== self::CACHE_KEY_AD_SLOT) {
            throw new InvalidArgumentException(sprintf('expect cache key %s', self::CACHE_KEY_AD_SLOT));
        }

        return parent::refreshForCacheKey($cacheKey, $model);
    }

    /**
     * @inheritdoc
     */
    public function getAdTagsForAdSlot($slotId)
    {
        $namespace = $this->getNamespace($slotId);
        $this->cache->setNamespace($namespace);
        $cacheKey = 'all_tags_array';

        $namespaceVersion = $this->cache->getNamespaceVersion(true); // version should be from redis cache not from memory to make sure it is in sync with tag cache
        $this->cache->setNamespaceVersion($namespaceVersion);

        if ($this->cache->contains($cacheKey)) {
            return $this->cache->fetch($cacheKey);
        }

        return false;
    }


    protected function refreshCacheForReferencingDynamicAdSlot(ReportableAdSlotInterface $adSlot)
    {
        //step 2. refresh cache for all affected DynamicAdSlots
        $affectedDynamicAdSlots = $this->getAffectedDynamicAdSlot($adSlot);
        foreach ($affectedDynamicAdSlots as $dynamicAdSlot) {
            $this->refreshCacheForDynamicAdSlot($dynamicAdSlot);
        }

        return $this;
    }

    /**
     * When an ad slot is updated, this will affect to all dynamic adslot that has reference to the updated ad slot
     * hence we have to refresh cache for all DynamicAdSlot[] referencing to this ad slot
     *
     * referencing include: expectedAdSlot and defaultAdSlot
     *
     * @param ReportableAdSlotInterface $updatingAdSlot
     * @return DynamicAdSlotInterface[]
     */
    private function getAffectedDynamicAdSlot(ReportableAdSlotInterface $updatingAdSlot)
    {
        $expressions = $this->expressionRepository->findBy(array('expectAdSlot' => $updatingAdSlot));

        $dynamicAdSlotsWithExpressionReference = array_map(
            function (ExpressionInterface $expression) {
                return $expression->getDynamicAdSlot();
            },
            $expressions
        );

        $dynamicAdSlotsWithDefaultAdSlotReference = $this->dynamicAdSlotManager->getDynamicAdSlotsThatHaveDefaultAdSlot($updatingAdSlot);
        $referencingDynamicAdSlots = array_merge($dynamicAdSlotsWithDefaultAdSlotReference, $dynamicAdSlotsWithExpressionReference);

        return array_unique($referencingDynamicAdSlots);
    }

    /**
     * create AdSlot Cache Data.
     *
     * In case of 'enableVariable == false' => formatted as 'display':
     * {
     *     "id": "1",
     *     "type": "display",
     *     "tags": [ {...}, [{...}, ...], ...]
     * }
     *
     * else, in case of 'enableVariable == true' => formatted as 'dynamic':
     * {
     *     "id": "1",
     *     "type": "dynamic",
     *     "expressions":
     *     [
     *         {
     *             "expression": ...,
     *             "expectAdSlot": ...
     *         },
     *         {...},
     *         ...
     *     ],
     *     "slots":
     *     [
     *         //array of 'display' format as above.
     *     ]
     * }
     *
     * @param BaseAdSlotInterface $adSlot
     * @return array
     */
    public function createAdSlotCacheData(BaseAdSlotInterface $adSlot)
    {
        return $this->createCacheDataForEntity($adSlot);
    }

    /**
     * @return TagGenerator
     */
    public function getTagGenerator()
    {
        return $this->tagGenerator;
    }

    protected function getDisplayBlacklistManager()
    {
        return $this->displayBlacklistManager;
    }

    /**
     * @return string
     */
    protected function getBlacklistPrefix()
    {
        return $this->blacklistPrefix;
    }
}