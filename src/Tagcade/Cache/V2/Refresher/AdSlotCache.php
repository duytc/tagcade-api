<?php

namespace Tagcade\Cache\V2\Refresher;


use Tagcade\Cache\Legacy\Cache\Tag\NamespaceCacheInterface;
use Tagcade\Cache\V2\Behavior\CreateAdSlotDataTrait;
use Tagcade\DomainManager\DisplayAdSlotManagerInterface;
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
use Tagcade\Repository\Core\ExpressionRepositoryInterface;
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

    public function __construct(NamespaceCacheInterface $cache,
        Manager $workerManager,
        DisplayAdSlotManagerInterface $displayAdSlotManager,
        NativeAdSlotManagerInterface $nativeAdSlotManager,
        DynamicAdSlotManagerInterface $dynamicAdSlotManager,
        ExpressionRepositoryInterface $expressionRepository)
    {
        parent::__construct($cache, $workerManager);

        $this->expressionRepository = $expressionRepository;
        $this->dynamicAdSlotManager = $dynamicAdSlotManager;
        $this->displayAdSlotManager = $displayAdSlotManager;
        $this->nativeAdSlotManager = $nativeAdSlotManager;
    }

    public function refreshCache()
    {
        $adSlots = $this->displayAdSlotManager->all();
        foreach ($adSlots as $adSlot) {
            $this->refreshCacheForDisplayAdSlot($adSlot, false);
        }

        $nativeAdSlots = $this->nativeAdSlotManager->all();
        foreach ($nativeAdSlots as $nativeAdSlot) {
            $this->refreshCacheForNativeAdSlot($nativeAdSlot, false);
        }

        $dynamicAdSlots = $this->dynamicAdSlotManager->all();
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

    public function refreshCacheForNativeAdSlot(NativeAdSlotInterface $nativeAdSlot,  $alsoRefreshRelatedDynamicAdSlot = true)
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


    public function refreshForCacheKey($cacheKey, ModelInterface $model)
    {
        if ($cacheKey !== self::CACHE_KEY_AD_SLOT) {
            throw new InvalidArgumentException(sprintf('expect cache key %s', self::CACHE_KEY_AD_SLOT));
        }

        return parent::refreshForCacheKey($cacheKey, $model);
    }

    public function getAdTagsForAdSlot($slotId)
    {
        $namespace= $this->getNamespace($slotId);
        $this->cache->setNamespace($namespace);
        $cacheKey = 'all_tags_array';

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
            function(ExpressionInterface $expression)
            {
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


} 