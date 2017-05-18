<?php

namespace Tagcade\Worker\Workers;

use StdClass;
use Tagcade\Cache\V2\TagCacheV2Interface;
use Tagcade\DomainManager\AdNetworkManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;

// responsible for doing the background tasks assigned by the manager
// all public methods on the class represent tasks that can be done

class UpdateAdSlotCacheDueToDisplayBlacklistWorker
{
    /** @var TagCacheV2Interface */
    private $tagCache;

    /** @var AdSlotRepositoryInterface */
    private $adSlotRepository;

    /** @var AdNetworkManagerInterface */
    private $adNetworkManager;

    /**
     * UpdateAdSlotCacheDueToDisplayBlacklistWorker constructor.
     *
     * @param TagCacheV2Interface $tagCache
     * @param AdNetworkManagerInterface $adNetworkManager
     * @param AdSlotRepositoryInterface $adSlotRepository
     */
    public function __construct(TagCacheV2Interface $tagCache, AdNetworkManagerInterface $adNetworkManager, AdSlotRepositoryInterface $adSlotRepository)
    {
        $this->tagCache = $tagCache;
        $this->adNetworkManager = $adNetworkManager;
        $this->adSlotRepository = $adSlotRepository;
    }

    /**
     * update cache for multiple ad slot when ad network changed
     *
     * @param StdClass $params
     */
    public function updateAdSlotCacheForAdNetwork(StdClass $params)
    {
        $adNetworkId = $params->network;
        $adNetwork = $this->adNetworkManager->find($adNetworkId);

        if (!$adNetwork instanceof AdNetworkInterface) {
            throw new InvalidArgumentException(sprintf('ad network %d does not exist', $adNetworkId));
        }

        $adSlots = $this->adSlotRepository->getReportableAdSlotRelatedAdNetwork($adNetwork);

        foreach ($adSlots as $adSlot) {
            /** @var DisplayAdSlotInterface|ReportableAdSlotInterface $adSlot */
            if (!$adSlot instanceof DisplayAdSlotInterface && !$adSlot instanceof NativeAdSlotInterface) {
                continue; // only supported DisplayAdSlot and rtbStatus is enabled
            }

            $this->tagCache->refreshCacheForReportableAdSlot($adSlot, true);
        }
    }
}