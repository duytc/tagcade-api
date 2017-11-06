<?php

namespace Tagcade\Worker\Workers;

use stdClass;
use Tagcade\Cache\V2\TagCacheV2Interface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;

class UpdateAdSlotCacheWorker
{
    const JOB_NAME = 'updateCacheForAdSlot';
    const PARAM_AD_SLOT = 'adSlot';

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
    public function updateCacheForAdSlot(stdClass $params)
    {
        $adSlotId = $params->adSlot;
        $adSlot = $this->adSlotManager->find($adSlotId);

        if (!$adSlot instanceof BaseAdSlotInterface) {
            throw new InvalidArgumentException(sprintf('ad slot %d does not exist', $adSlotId));
        }

        if ($adSlot instanceof ReportableAdSlotInterface) {
            $this->tagCache->refreshCacheForReportableAdSlot($adSlot, true);
        }

        if ($adSlot instanceof DynamicAdSlotInterface) {
            $this->tagCache->refreshCacheForDynamicAdSlot($adSlot);
        }
    }
}