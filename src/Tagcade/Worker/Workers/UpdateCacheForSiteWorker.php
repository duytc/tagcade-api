<?php

namespace Tagcade\Worker\Workers;

use StdClass;
use Tagcade\Cache\V2\TagCacheV2Interface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;

// responsible for doing the background tasks assigned by the manager
// all public methods on the class represent tasks that can be done

class UpdateCacheForSiteWorker
{
    /** @var TagCacheV2Interface */
    private $tagCache;

    /** @var SiteManagerInterface */
    private $siteManager;

    /** @var AdSlotManagerInterface */
    private $adSlotManager;

    function __construct(TagCacheV2Interface $tagCache, SiteManagerInterface $siteManager, AdSlotManagerInterface $adSlotManager)
    {
        $this->tagCache = $tagCache;
        $this->siteManager = $siteManager;
        $this->adSlotManager = $adSlotManager;
    }

    /**
     * update cache for multiple site ids
     *
     * @param StdClass $params
     */
    public function updateCacheForSites(StdClass $params)
    {
        $siteIds = $params->siteIds;

        if (!is_array($siteIds)) {
            throw new InvalidArgumentException('site ids must be an int array');
        }

        foreach ($siteIds as $siteId) {
            /** @var SiteInterface $site */
            $site = $this->siteManager->find($siteId);

            if (!$site instanceof SiteInterface) {
                throw new InvalidArgumentException('That site does not exist');
            }

            $adSlots = $this->adSlotManager->getAdSlotsForSite($site);

            foreach ($adSlots as $adSlot) {
                /** @var DisplayAdSlotInterface|ReportableAdSlotInterface $adSlot */
                if (!$adSlot instanceof DisplayAdSlotInterface || !$adSlot->isRTBEnabled()) {
                    continue; // only supported DisplayAdSlot and rtbStatus is enabled
                }

                $this->tagCache->refreshCacheForReportableAdSlot($adSlot, true);
            }
        }
    }
}