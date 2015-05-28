<?php

namespace Tagcade\Cache\DynamicAdSlot;

use Tagcade\Cache\Legacy\Cache\Tag\NamespaceCacheInterface;
use Tagcade\Cache\TagCacheAbstract;
use Tagcade\Cache\TagCacheInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\DynamicAdSlotManagerInterface;
use Tagcade\Form\Type\DynamicAdSlotFormType;
use Tagcade\Form\Type\ExpressionFormType;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Repository\Core\ExpressionRepositoryInterface;

class TagCache extends TagCacheAbstract implements TagCacheInterface
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

    public function __construct(NamespaceCacheInterface $cache, AdSlotManagerInterface $adSlotManager, DynamicAdSlotManagerInterface $dynamicAdSlotManager, ExpressionRepositoryInterface $expressionRepository)
    {
        parent::__construct($cache, $adSlotManager);
        $this->expressionRepository = $expressionRepository;
        $this->dynamicAdSlotManager = $dynamicAdSlotManager;
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
        $affectedDynamicAdSlots = $this->getAffectedDynamicAdSlot($adSlot);
        foreach ($affectedDynamicAdSlots as $dynamicAdSlot) {
            $this->refreshCacheForDynamicAdSlot($dynamicAdSlot);
        }

        return $this;
    }

    /**
     * refresh cache for DynamicAdSlot
     * @param DynamicAdSlotInterface $dynamicAdSlot
     * @return $this
     */
    public function refreshCacheForDynamicAdSlot(DynamicAdSlotInterface $dynamicAdSlot)
    {
        $this->cache->setNamespace($this->getNamespace($dynamicAdSlot->getId()));

        $oldVersion = (int)$this->cache->getNamespaceVersion();
        $newVersion = $oldVersion + 1;

        // create the new version of the cache first
        $this->cache->setNamespaceVersion($newVersion);
        $this->cache->save(self::CACHE_KEY_AD_SLOT, $this->createAdSlotCacheDataDynamic($dynamicAdSlot));

        // delete the old version of the cache
        $this->cache->setNamespaceVersion($oldVersion);

        $this->cache->deleteAll();

        return $this;
    }

    /**
     * create AdSlot Cache Data.
     *
     * In case of 'enableVariable == false' => formatted as 'static':
     * {
     *     "id": "1",
     *     "type": "static",
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
     *         //array of 'static' format as above.
     *     ]
     * }
     *
     * @param AdSlotInterface $adSlot
     * @return array
     */
    protected function createAdSlotCacheData(AdSlotInterface $adSlot)
    {
        return $this->createAdSlotCacheDataStatic($adSlot);
    }

    /**
     * When an ad slot is updated, this will affect to all dynamic adslot that has reference to the updated ad slot
     * hence we have to refresh cache for all DynamicAdSlot[] referencing to this ad slot
     *
     * referencing include: expectedAdSlot and defaultAdSlot
     *
     * @param AdSlotInterface $updatingAdSlot
     * @return DynamicAdSlotInterface[]
     */
    private function getAffectedDynamicAdSlot(AdSlotInterface $updatingAdSlot)
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


    /**
     * create as dynamic, format as:
     *
     * [
     *     'id' => $adSlot->getId(),
     *     'type' => 'static',
     *     'expressions' => [
     *          'expressions' => [... expression, expectAdSlot ...],
     *          'defaultAdSlot'
     *     ],
     *     'slots' => [... array of static-adSlot-cache ...]
     * ]
     *
     * @param DynamicAdSlotInterface $dynamicAdSlot
     * @return array
     */
    private function createAdSlotCacheDataDynamic(DynamicAdSlotInterface $dynamicAdSlot)
    {
        $data = [
            'id' => $dynamicAdSlot->getId(),
            'type' => 'dynamic',
            'expressions' => [],
            'defaultAdSlot' => $dynamicAdSlot->getDefaultAdSlot() instanceof AdSlotInterface ? $dynamicAdSlot->getDefaultAdSlot()->getId() : null,
            'slots' => []
        ];

        ////adSlot (as defaultAdSlot) from DynamicAdSlot:
        $adSlotsForSelecting = array();
        if ($dynamicAdSlot->getDefaultAdSlot() instanceof AdSlotInterface) {
            $adSlotsForSelecting[] = $dynamicAdSlot->getDefaultAdSlot();
        }

        //check expressions
        /** @var ExpressionInterface[] $expressions */
        $expressions = $dynamicAdSlot->getExpressions()->toArray();
        if (is_array($expressions) && !empty($expressions)) {
            //step 1. set 'expressions' for data: get expressionInJS of each expression in expressions
            array_walk($expressions,
                function(ExpressionInterface $expression ) use (&$data){
                    array_push($data['expressions'], $expression->getExpressionInJs());
                }
            );

            //step 2. get all AdSlots related to DynamicAdSlot and Expressions
            ////adSlots from expressionInJS of Expressions
            $tmpAdSlotsForSelecting = array_map(function (ExpressionInterface $expression) {
                    return $expression->getExpectAdSlot();
                },
                $expressions
            );

            $adSlotsForSelecting = array_merge($adSlotsForSelecting, $tmpAdSlotsForSelecting);
        }



        $adSlotsForSelecting = array_unique($adSlotsForSelecting);

        //step 3. build 'slots' for data
        array_walk($adSlotsForSelecting, function(AdSlotInterface $adSlot) use (&$data){
                $data['slots'][$adSlot->getId()] =  $this->createAdSlotCacheDataStatic($adSlot);
            }
        );

        //step 5. return data
        return $data;
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