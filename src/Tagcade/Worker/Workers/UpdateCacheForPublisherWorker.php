<?php

namespace Tagcade\Worker\Workers;

use StdClass;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Cache\V2\TagCacheV2Interface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\RonAdSlotManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\User\Role\PublisherInterface;

// responsible for doing the background tasks assigned by the manager
// all public methods on the class represent tasks that can be done

class UpdateCacheForPublisherWorker
{
    /** @var TagCacheV2Interface */
    private $tagCache;

    /** @var PublisherManagerInterface */
    private $publisherManager;

    /** @var AdSlotManagerInterface */
    private $adSlotManager;

    /** @var RonAdSlotManagerInterface */
    private $ronAdSlotManager;

    function __construct(TagCacheV2Interface $tagCache,
                         PublisherManagerInterface $publisherManager, AdSlotManagerInterface $adSlotManager, RonAdSlotManagerInterface $ronAdSlotManager)
    {
        $this->tagCache = $tagCache;
        $this->publisherManager = $publisherManager;
        $this->adSlotManager = $adSlotManager;
        $this->ronAdSlotManager = $ronAdSlotManager;
    }

    /**
     * update cache for multiple publisher ids
     *
     * @param StdClass $params
     */
    public function updateCacheForPublishers(StdClass $params)
    {
        $publisherIds = $params->publisherIds;

        if (!is_array($publisherIds)) {
            throw new InvalidArgumentException('publisher ids must be an int array');
        }

        foreach ($publisherIds as $publisherId) {
            /** @var PublisherInterface $publisher */
            $publisher = $this->publisherManager->find($publisherId);

            if (!$publisher instanceof PublisherInterface) {
                throw new InvalidArgumentException('That publisher does not exist');
            }

            // update cache for all DISPLAY(!!!) ad slots of this publisher
            $adSlots = $this->adSlotManager->getDisplayAdSlotsForPublisher($publisher);
            foreach ($adSlots as $adSlot) {
                $this->tagCache->refreshCacheForReportableAdSlot($adSlot, true);
            }

            // update cache for all ron DISPLAY(!!!) ad slots of this publisher
            $ronAdSlots = $this->ronAdSlotManager->getRonDisplayAdSlotsForPublisher($publisher);
            foreach ($ronAdSlots as $ronAdSlot) {
                $this->tagCache->refreshCacheForRonAdSlot($ronAdSlot, true);
            }
        }
    }
}