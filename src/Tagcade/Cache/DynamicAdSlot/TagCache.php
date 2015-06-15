<?php

namespace Tagcade\Cache\DynamicAdSlot;

use Tagcade\Cache\DynamicAdSlot\Behavior\CreateAdSlotDataTrait;
use Tagcade\Cache\Legacy\Cache\Tag\NamespaceCacheInterface;
use Tagcade\Cache\TagCacheAbstract;
use Tagcade\Cache\TagCacheInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\DynamicAdSlotManagerInterface;
use Tagcade\DomainManager\NativeAdSlotManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Repository\Core\ExpressionRepositoryInterface;

class TagCache extends TagCacheAbstract implements TagCacheInterface, TagCacheV2Interface
{
    use CreateAdSlotDataTrait;
    const VERSION = 2;
    const NAMESPACE_CACHE_KEY = 'tagcade_adslot_v2_%d';

    /** key expressions  for ad slot dynamic select */
    const KEY_EXPRESSIONS = 'expressions';
    /** key defaultAdSlot for ad slot dynamic select */
    const KEY_DEFAULT_AD_SLOT = 'defaultAdSlot';

    /**
     * @var ExpressionRepositoryInterface
     */
    protected $expressionRepository;
    /**
     * @var DynamicAdSlotManagerInterface
     */
    private $dynamicAdSlotManager;
    /**
     * @var NativeAdSlotManagerInterface
     */
    private $nativeAdSlotManager;

    public function __construct(NamespaceCacheInterface $cache,
        AdSlotManagerInterface $adSlotManager,
        DynamicAdSlotManagerInterface $dynamicAdSlotManager,
        NativeAdSlotManagerInterface $nativeAdSlotManager,
        ExpressionRepositoryInterface $expressionRepository)
    {
        parent::__construct($cache, $adSlotManager);
        $this->expressionRepository = $expressionRepository;
        $this->dynamicAdSlotManager = $dynamicAdSlotManager;
        $this->nativeAdSlotManager = $nativeAdSlotManager;
    }

    /**
     * refresh Cache
     * @return $this
     */
    public function refreshCache()
    {
        $adSlots = $this->adSlotManager->all();
        foreach ($adSlots as $adSlot) {
            $this->refreshCacheForAdSlot($adSlot, false);
        }

        $nativeAdSlots = $this->nativeAdSlotManager->all();
        foreach ($nativeAdSlots as $nativeAdSlot) {
            $this->refreshCacheForNativeAdSlot($nativeAdSlot);
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
    public function refreshCacheForAdSlot(AdSlotInterface $adSlot, $alsoRefreshRelatedDynamicAdSlot = true)
    {
        //step 1. refresh cache for AdSlot
        parent::refreshCacheForAdSlot($adSlot);

        if (!$alsoRefreshRelatedDynamicAdSlot) {
            return $this;
        }

        //step 2. refresh cache for all affected DynamicAdSlots
        return $this->refreshCacheForReferencingDynamicAdSlot($adSlot);
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


    public function refreshCacheForNativeAdSlot(NativeAdSlotInterface $nativeAdSlot,  $alsoRefreshRelatedDynamicAdSlot = true)
    {
        $this->refreshForCacheKey(self::CACHE_KEY_AD_SLOT, $nativeAdSlot);

        if (!$alsoRefreshRelatedDynamicAdSlot) {
            return $this;
        }

        //step 2. refresh cache for all affected DynamicAdSlots
        return $this->refreshCacheForReferencingDynamicAdSlot($nativeAdSlot);
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

    protected function refreshForCacheKey($cacheKey, ModelInterface $model)
    {
        if ($cacheKey !== self::CACHE_KEY_AD_SLOT) {
            throw new InvalidArgumentException( sprintf('expect cache key %s', self::CACHE_KEY_AD_SLOT));
        }

        $this->cache->setNamespace($this->getNamespace($model->getId()));

        $oldVersion = (int)$this->cache->getNamespaceVersion();
        $newVersion = $oldVersion + 1;

        // create the new version of the cache first
        $this->cache->setNamespaceVersion($newVersion);

        $this->cache->save(self::CACHE_KEY_AD_SLOT, $this->createCacheDataForEntity($model));

        // delete the old version of the cache
        $this->cache->setNamespaceVersion($oldVersion);

        $this->cache->deleteAll();

        return $this;
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
     * @param AdSlotInterface $adSlot
     * @return array
     */
    protected function createAdSlotCacheData(AdSlotInterface $adSlot)
    {
        return $this->createDisplayAdSlotCacheData($adSlot);
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

        $referencingDynamicAdSlots = array_map(
            function(ExpressionInterface $expression) {
                return $expression->getDynamicAdSlot();
            },
            $expressions
        );

        if ($updatingAdSlot->defaultDynamicAdSlots() != null && $updatingAdSlot->defaultDynamicAdSlots()->count() > 0) {
            $referencingDynamicAdSlots = array_merge($referencingDynamicAdSlots, $updatingAdSlot->defaultDynamicAdSlots()->toArray());
        }

        return array_unique($referencingDynamicAdSlots);
    }

    protected function getNamespace($slotId)
    {
        return sprintf(static::NAMESPACE_CACHE_KEY, $slotId);
    }

    public function supportVersion($version)
    {
        return $version === self::VERSION;
    }
}