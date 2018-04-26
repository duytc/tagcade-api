<?php

namespace Tagcade\Worker\Workers;

use stdClass;
use Tagcade\Cache\V2\TagCacheV2Interface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;

// responsible for doing the background tasks assigned by the manager
// all public methods on the class represent tasks that can be done

class UpdateAdSlotCacheWhenRemoveOptimizationIntegrationWorker
{
    /** @var TagCacheV2Interface */
    private $tagCache;

    /** @var AdSlotRepositoryInterface */
    private $adSlotManager;

    /**
     * UpdateAdSlotCacheDueToDisplayBlacklistWorker constructor.
     *
     * @param TagCacheV2Interface $tagCache
     * @param AdSlotManagerInterface $adSlotManager
     */
    public function __construct(TagCacheV2Interface $tagCache, AdSlotManagerInterface $adSlotManager)
    {
        $this->tagCache = $tagCache;
        $this->adSlotManager = $adSlotManager;
    }

    /**
     * update cache for multiple ad slot when ad network changed
     *
     * @param StdClass $params
     */
    public function updateAdSlotCacheWhenRemoveOptimizationIntegration(stdClass $params)
    {
        $adSlotIds = $params->adSlots;
        if (!is_array($adSlotIds) || empty($adSlotIds)) {
            return;
        }

        foreach ($adSlotIds as $adSlotId) {
            $adSlot = $this->adSlotManager->find($adSlotId);

            if (!$adSlot instanceof BaseAdSlotInterface) {
                throw new InvalidArgumentException(sprintf('ad slot %d does not exist', $adSlotId));
            }

            if (!$adSlot instanceof ReportableAdSlotInterface || !$adSlot instanceof DisplayAdSlotInterface) {
                return;
            }

            $cacheKeysToBeRemoved = ['autoOptimize'];
            $this->tagCache->removeKeysInSlotCacheForDisplayAdSlot($adSlot, $cacheKeysToBeRemoved, true);
        }
    }
}