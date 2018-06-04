<?php

namespace Tagcade\Cache\V2\Refresher;


use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Tagcade\Bundle\ApiBundle\Service\ExpressionInJsGeneratorInterface;
use Tagcade\Cache\CacheNamespace\NamespaceCacheInterface;
use Tagcade\Cache\V2\Behavior\CreateAdSlotDataTrait;
use Tagcade\Cache\V2\DisplayBlacklistCacheManagerInterface;
use Tagcade\DomainManager\DisplayAdSlotManagerInterface;
use Tagcade\DomainManager\DynamicAdSlotManagerInterface;
use Tagcade\DomainManager\NativeAdSlotManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdTagInterface;
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

    /** @var DynamicAdSlotManagerInterface */
    protected $dynamicAdSlotManager;

    /** @var ExpressionRepositoryInterface */
    protected $expressionRepository;

    /** @var DisplayAdSlotManagerInterface */
    private $displayAdSlotManager;

    /** @var NativeAdSlotManagerInterface */
    private $nativeAdSlotManager;

    /** @var TagGenerator */
    private $tagGenerator;

    /** @var DisplayBlacklistCacheManagerInterface $displayBlacklistCacheManager */
    protected $displayBlacklistCacheManager;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var ExpressionInJsGeneratorInterface */
    protected $expressionInJsGenerator;

    /** @var string */
    protected $blacklistPrefix;

    /** @var string */
    protected $whiteListPrefix;

    public function __construct(EntityManagerInterface $em, NamespaceCacheInterface $cache, Manager $workerManager,
                                DisplayAdSlotManagerInterface $displayAdSlotManager, NativeAdSlotManagerInterface $nativeAdSlotManager,
                                DynamicAdSlotManagerInterface $dynamicAdSlotManager, ExpressionRepositoryInterface $expressionRepository,
                                TagGenerator $tagGenerator, $blacklistPrefix, $whiteListPrefix, ExpressionInJsGeneratorInterface $expressionInJsGenerator)
    {
        parent::__construct($cache, $workerManager);

        $this->expressionRepository = $expressionRepository;
        $this->dynamicAdSlotManager = $dynamicAdSlotManager;
        $this->displayAdSlotManager = $displayAdSlotManager;
        $this->nativeAdSlotManager = $nativeAdSlotManager;
        $this->tagGenerator = $tagGenerator;
        $this->expressionInJsGenerator = $expressionInJsGenerator;
        $this->blacklistPrefix = $blacklistPrefix;
        $this->whiteListPrefix = $whiteListPrefix;
        $this->em = $em;
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
    public function refreshCacheForDisplayAdSlot(DisplayAdSlotInterface $adSlot, $alsoRefreshRelatedDynamicAdSlot = true, $extraData = [])
    {
        if (empty($extraData)) {
            // sync version
            $this->cache->setNamespace($this->getNamespaceByEntity($adSlot));
            $oldVersion = (int)$this->cache->getNamespaceVersion($forceFromCache = true);
            $this->cache->setNamespaceVersion($oldVersion);

            $extraData = $this->getAutoOptimizeCacheForAdSlot($adSlot, self::CACHE_KEY_AD_SLOT);
            $extraData = $this->refreshOptimizationData($extraData, $adSlot);
        }

        if (!$adSlot->isAutoOptimize()) {
            $extraData = [];
        }

        //step 1. refresh cache for AdSlot
        $this->refreshForCacheKey(self::CACHE_KEY_AD_SLOT, $adSlot, $extraData);

        if (!$alsoRefreshRelatedDynamicAdSlot) {
            return $this;
        }

        //step 2. refresh cache for all affected DynamicAdSlots
        return $this->refreshCacheForReferencingDynamicAdSlot($adSlot);
    }

    /**
     * @inheritdoc
     */
    public function removeKeysInSlotCacheForDisplayAdSlot(DisplayAdSlotInterface $adSlot, array $cacheKeys, $alsoRefreshRelatedDynamicAdSlot = true)
    {
        if (empty($cacheKeys)) {
            return $this;
        }

        // sync version
        $this->cache->setNamespace($this->getNamespaceByEntity($adSlot));
        $oldVersion = (int)$this->cache->getNamespaceVersion($forceFromCache = true);
        $this->cache->setNamespaceVersion($oldVersion);

        // get current cache
        $cache = $this->cache->fetch(self::CACHE_KEY_AD_SLOT);
        if (!is_array($cache)) {
            return $this;
        }

        // remove cache keys from cache
        foreach ($cacheKeys as $cacheKey) {
            if (!array_key_exists($cacheKey, $cache)) {
                continue;
            }

            // remove cache key
            unset($cache[$cacheKey]);
        }

        // save
        $newVersion = $oldVersion + 1;
        $this->cache->setNamespaceVersion($newVersion);
        $this->cache->save(self::CACHE_KEY_AD_SLOT, $cache);
        $this->cache->deleteAll();

        // refresh cache for all affected DynamicAdSlots
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
    public function refreshForCacheKey($cacheKey, ModelInterface $model, $extraData = [])
    {
        if ($cacheKey !== self::CACHE_KEY_AD_SLOT) {
            throw new InvalidArgumentException(sprintf('expect cache key %s', self::CACHE_KEY_AD_SLOT));
        }

        return parent::refreshForCacheKey($cacheKey, $model, $extraData);
    }

    /**
     * @inheritdoc
     */
    public function getAdTagsForAdSlot($slotId)
    {
        $namespace = $this->getNamespace($slotId);
        $this->cache->setNamespace($namespace);
        $cacheKey = 'all_tags_array';

        $namespaceVersion = $this->cache->getNamespaceVersion($forceFromCache = true); // version should be from redis cache not from memory to make sure it is in sync with tag cache
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

    /**
     * @return string
     */
    protected function getBlacklistPrefix()
    {
        return $this->blacklistPrefix;
    }

    protected function getWhiteListPrefix()
    {
        return $this->whiteListPrefix;
    }

    protected function getEntityManager()
    {
        return $this->em;
    }

    protected function getExpressionInJsGenerator()
    {
        return $this->expressionInJsGenerator;
    }

    /**
     * @param $extraData
     * @param DisplayAdSlotInterface $adSlot
     * @return mixed
     */
    private function refreshOptimizationData($extraData, DisplayAdSlotInterface $adSlot)
    {
        if (!is_array($extraData) || !array_key_exists('autoOptimize', $extraData)) {
            return $extraData;
        }

        $extraData = $extraData['autoOptimize'];
        $adTags = $adSlot->getAdTags();
        $adTags = $adTags instanceof Collection ? $adTags->toArray() : $adTags;
        $adTags = is_array($adTags) ? $adTags : [$adTags];
        $newAdTags = [];
        foreach ($adTags as $adTag) {
            if (!$adTag instanceof AdTagInterface) {
                continue;
            }
            $newAdTags[$adTag->getId()] = $adTag;
        }

        //Process default
        if (array_key_exists('default', $extraData)) {
            $extraData['default'] = $this->refreshAdSlotKeys($extraData['default'], $newAdTags);
        }

        //Process other keys
        foreach ($extraData as $key => $data) {
            if ($key == 'default') {
                continue;
            }

            foreach ($data as $identifier => $score) {
                $score = $this->refreshAdSlotKeys($score, $newAdTags);
                $data[$identifier] = $score;
            }

            $extraData[$key] = $data;
        }

        return ['autoOptimize' => $extraData];
    }

    /**
     * @param array $score
     * @param array $adTags
     * @return array
     */
    private function refreshAdSlotKeys(array $score, array $adTags)
    {
        // do not use array_diff when optimizecache supports the same positions for adTags
//        $newTagIds = array_diff($adTagIds, $score);
//        $score = array_merge($score, $newTagIds);
        $score = $this->addMissingAdTags($score, $adTags);
        /*
         * scores [ 1, 2, 3, 4]
         */

        foreach ($score as $key => $adTagId) {
            if (is_array($adTagId)) {
                foreach ($adTagId as $keyItem => $adTagIdItem) {
                    if (!array_key_exists($adTagIdItem, $adTags)) {
                        unset($adTagId[$keyItem]);
                        continue;
                    }

                    $adTag = $adTags[$adTagIdItem];
                    if (!$adTag instanceof AdTagInterface || !$adTag->isActive()) {
                        unset($adTagId[$keyItem]);
                        continue;
                    }
                }

                if (isset($adTagId) && !empty($adTagId)) {
                    $score[$key] = $adTagId;
                } else {
                    unset($score[$key]);
                }

                continue;
            }

            if (!array_key_exists($adTagId, $adTags)) {
                unset($score[$key]);
                continue;
            }

            $adTag = $adTags[$adTagId];
            if (!$adTag instanceof AdTagInterface || !$adTag->isActive()) {
                unset($score[$key]);
                continue;
            }
        }

        $score = array_values($score);

        // do pin ad tags
        $score = $this->doPinAdTags($score, $adTags);

        return $score;
    }

    /**
     * @param $orderAdTagIds
     * @param $adTags
     * @return mixed
     */
    private function addMissingAdTags($orderAdTagIds, $adTags)
    {
        $adTags = array_filter($adTags, function ($adTag) {
            return $adTag instanceof AdTagInterface;
        });

        usort($adTags, function ($adTag1, $adTag2) {
            /** @var AdTagInterface $adTag1 */
            /** @var AdTagInterface $adTag2 */
            if ($adTag1->getPosition() === $adTag2->getPosition()) {
                return 0;
            }

            return ($adTag1->getPosition() < $adTag2->getPosition()) ? -1 : 1;
        });

        foreach ($adTags as $adTag) {
            if (!$adTag instanceof AdTagInterface) {
                continue;
            }

            $adTagId = $adTag->getId();

            // need to support adTag the same position
            $adTagIdExisted = false;
            foreach ($orderAdTagIds as $orderAdTagId) {

                if (!is_array($orderAdTagId) || empty($orderAdTagId)) {
                    continue;
                }

                // if $adTagId existed in $orderAdTagId -> continue: do not need to add in to  $orderAdTagIds
                if (in_array($adTagId, $orderAdTagId)) {
                    $adTagIdExisted = true;
                    break;
                }
            }

            // check adTAgIdExisted
            if ($adTagIdExisted == true) {
                continue;
            }

            // add missing adTags
            if (!in_array($adTagId, $orderAdTagIds)) {
                $orderAdTagIds [] = $adTagId;
            }
        }

        unset($adTagId, $adTag, $orderAdTagId);
        return $orderAdTagIds;
    }
    /**
     * @param array $score
     * @param array $adTags
     * @return mixed
     */
    private function doPinAdTags(array $score, array $adTags)
    {
        //do pin same as in update cache when receive scores... (function handleKeepAdTagsPositionWithPinned)
        // get adTags need to be pinned
        $adTagsNeedToBePinned = array_filter($adTags, function ($adTag) {
            return $adTag instanceof AdTagInterface && $adTag->isActive() && $adTag->isPin();
        });

        //// sort $adTagsNeedToBePinned by position asc
        usort($adTagsNeedToBePinned, function ($adTag1, $adTag2) {
            /** @var AdTagInterface $adTag1 */
            /** @var AdTagInterface $adTag2 */
            if ($adTag1->getPosition() === $adTag2->getPosition()) {
                return 0;
            }

            return ($adTag1->getPosition() < $adTag2->getPosition()) ? -1 : 1;
        });

        //// remove needed pin ad tags from $score
        //// we will add them to $score again by their positions
        foreach ($score as $key => $adTagId) {

            // pin adTag always is one position, can not the same position with other adTags
            if (is_array($adTagId)) {
                continue;
            }

            $adTag = $adTags[$adTagId];
            if (!$adTag instanceof AdTagInterface) {
                continue;
            }
            if ($adTag->isActive() && $adTag->isPin()) {
                unset($score[$key]);
                continue;
            }
        }

        $score = array_values($score);

        //// do pin for needed pin ad tags
        foreach ($adTagsNeedToBePinned as $adTagNeedToBePinned) {
            if (!$adTagNeedToBePinned instanceof AdTagInterface) {
                continue;
            }
            $adTagPos = $adTagNeedToBePinned->getPosition();

            // append to end of $orderOptimizedAdTagIds if over length of $orderOptimizedAdTagIds
            // e.g $optimizedScore is [ 4, 2, 1 ] and ad tag 3 has position = 6, ad tag 5 has position = 8 (notice: 6 and 8 because we removed some paused ad tags before)
            // then expected $orderOptimizedAdTagIds is [ 4, 2, 1, 3, 5 ]
            if ($adTagPos > count($score)) {
                $score[] = $adTagNeedToBePinned->getId();
                continue;
            }

            // else, insert into middle...
            array_splice($score, $adTagPos - 1, 0, [$adTagNeedToBePinned->getId()]); // splice in at $adTagPos
        }

        return $score;
    }
}