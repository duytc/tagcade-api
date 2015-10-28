<?php

namespace Tagcade\Cache\V2\Refresher;


use Tagcade\Cache\Legacy\Cache\Tag\NamespaceCacheInterface;
use Tagcade\Cache\V2\Behavior\CreateRonAdSlotDataTrait;
use Tagcade\DomainManager\RonAdSlotManagerInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\Core\ReportableLibraryAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Repository\Core\LibraryDynamicAdSlotRepositoryInterface;
use Tagcade\Repository\Core\LibraryExpressionRepositoryInterface;
use Tagcade\Worker\Manager;

class RonAdSlotCache extends RefresherAbstract implements RonAdSlotCacheInterface
{
    use CreateRonAdSlotDataTrait;
    /**
     * @var RonAdSlotManagerInterface
     */
    private $ronAdSlotManager;
    /**
     * @var LibraryExpressionRepositoryInterface
     */
    private $libExpressionRepository;
    /**
     * @var LibraryDynamicAdSlotRepositoryInterface
     */
    private $libDynamicAdSlotRepository;

    function __construct(NamespaceCacheInterface $cache,
        Manager $workerManager,
        RonAdSlotManagerInterface $ronAdSlotManager,
        LibraryExpressionRepositoryInterface $libExpressionRepository,
        LibraryDynamicAdSlotRepositoryInterface $libDynamicAdSlotRepository
    )
    {
        parent::__construct($cache, $workerManager);
        $this->ronAdSlotManager = $ronAdSlotManager;
        $this->libExpressionRepository = $libExpressionRepository;
        $this->libDynamicAdSlotRepository = $libDynamicAdSlotRepository;
    }

    /**
     * public api, refresh Cache of all ad slot (display, native, dynamic, ron ad slot)
     * @return mixed
     */
    public function refreshCache()
    {
        /** @var RonAdSlotInterface[] $ronAdSlots */
        $ronAdSlots = $this->ronAdSlotManager->all();
        $dynamicRonAdSlots = [];

        // refresh cache for ron display and native first. Otherwise values (referencing ron slot) in Dynamic ron will not be updated.
        foreach ($ronAdSlots as $ronAdSlot) {
            if (!$ronAdSlot->getLibraryAdSlot() instanceof LibraryDynamicAdSlotInterface) {
                $this->refreshCacheForRonAdSlot($ronAdSlot, false);
                continue;
            }
            $dynamicRonAdSlots[] = $ronAdSlot;
        }

        foreach($dynamicRonAdSlots as $ronAdSlot) {
            $this->refreshCacheForRonAdSlot($ronAdSlot);
        }
    }

    public function refreshCacheForRonAdSlot(RonAdSlotInterface $ronAdSlot, $alsoRefreshRelatedDynamicRonAdSlot = false)
    {
        $this->refreshForCacheKey(self::CACHE_KEY_AD_SLOT, $ronAdSlot);
        if (!$alsoRefreshRelatedDynamicRonAdSlot) {
            return $this;
        }

        return $this->refreshCacheForReferencingDynamicRonAdSlot($ronAdSlot);
    }

    /**
     *
     * @param $ronAdSlotId
     * @return string|false jsons tring of ron slot tags data
     */
    public function getAdTagsForRonAdSlot($ronAdSlotId)
    {
        $namespace = $this->getNamespace($ronAdSlotId);
        $this->cache->setNamespace($namespace);

        $cacheKey = 'all_tags_array';

        if ($this->cache->contains($cacheKey)) {
            return $this->cache->fetch($cacheKey);
        }

        return false;
    }

    public function getNamespace($slotId)
    {
        return sprintf(self::NAMESPACE_RON_AD_SLOT_CACHE_KEY, $slotId);
    }


    /**
     * refresh Cache For Referencing Dynamic Ron Ad Slot (ron ad slot related to a library dynamic ad slot)
     * @param RonAdSlotInterface $ronAdSlot
     * @return $this
     */
    private function refreshCacheForReferencingDynamicRonAdSlot(RonAdSlotInterface $ronAdSlot)
    {
        $adSlot = $ronAdSlot->getLibraryAdSlot();
        if (!($adSlot instanceof ReportableLibraryAdSlotInterface)) {
            return $this;
        }
        //step 2. refresh cache for all affected DynamicAdSlots
        $affectedDynamicRonAdSlots = $this->getAffectedDynamicRonAdSlot($ronAdSlot);
        foreach ($affectedDynamicRonAdSlots as $dynamicRonAdSlot) {
            $this->refreshCacheForRonAdSlot($dynamicRonAdSlot);
        }

        return $this;
    }

    /**
     * When an library ad slot is updated, this will affect to all library dynamic ad slots that has reference to the updated ad slot, then affect to all ron ad slot related to them.
     * hence we have to refresh cache for all Dynamic Ron Ad Slots referencing to this library ad slot
     *
     * referencing include: expectedLibraryAdSlot and defaultLibraryAdSlot
     *
     * @param RonAdSlotInterface $updatingAdSlot which RELATES to a ReportableLibraryAdSlot
     * @return RonAdSlotInterface[]
     */
    private function getAffectedDynamicRonAdSlot(RonAdSlotInterface $updatingAdSlot)
    {
        $libAdSlot = $updatingAdSlot->getLibraryAdSlot();
        if (!($libAdSlot instanceof ReportableLibraryAdSlotInterface)) {
            return [];
        }

        $libExpressions = $this->libExpressionRepository->findBy(array('expectLibraryAdSlot' => $libAdSlot));
        $libDynamicAdSlotsWithLibExpressionReference = array_map(
            function (LibraryExpressionInterface $libExpression) {
                return $libExpression->getLibraryDynamicAdSlot();
            },
            $libExpressions
        );

        $dynamicAdSlotsWithDefaultAdSlotReference = $this->libDynamicAdSlotRepository->getByDefaultLibraryAdSlot($libAdSlot);
        $referencingLibDynamicAdSlots = array_merge($dynamicAdSlotsWithDefaultAdSlotReference, $libDynamicAdSlotsWithLibExpressionReference);

        $dynamicRonSlots = [];
        foreach ($referencingLibDynamicAdSlots as $libDynamic) {
            /**
             * @var LibraryDynamicAdSlotInterface $libDynamic
             */
            $ronSlot = $libDynamic->getRonAdSlot();
            if (!$ronSlot instanceof RonAdSlotInterface || in_array($ronSlot, $dynamicRonSlots)) {
                continue;
            }

            $dynamicRonSlots[] = $ronSlot;
        }

        return $dynamicRonSlots;
    }
}