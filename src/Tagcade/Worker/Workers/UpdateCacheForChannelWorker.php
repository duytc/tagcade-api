<?php

namespace Tagcade\Worker\Workers;

use StdClass;
use Tagcade\Cache\V2\TagCacheV2Interface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\ChannelManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;

// responsible for doing the background tasks assigned by the manager
// all public methods on the class represent tasks that can be done

class UpdateCacheForChannelWorker
{
    /** @var TagCacheV2Interface */
    private $tagCache;

    /** @var ChannelManagerInterface */
    private $channelManager;

    /** @var AdSlotManagerInterface */
    private $adSlotManager;

    function __construct(TagCacheV2Interface $tagCache, ChannelManagerInterface $channelManager, AdSlotManagerInterface $adSlotManager)
    {
        $this->tagCache = $tagCache;
        $this->channelManager = $channelManager;
        $this->adSlotManager = $adSlotManager;
    }

    /**
     * update cache for multiple channel ids
     *
     * @param StdClass $params
     */
    public function updateCacheForChannels(StdClass $params)
    {
        $channelIds = $params->channelIds;

        if (!is_array($channelIds)) {
            throw new InvalidArgumentException('site ids must be an int array');
        }

        foreach ($channelIds as $channelId) {
            /** @var ChannelInterface $channel */
            $channel = $this->channelManager->find($channelId);

            if (!$channel instanceof ChannelInterface) {
                throw new InvalidArgumentException('That Channel site does not exist');
            }

            $adSlots = $this->adSlotManager->getAdSlotsForChannel($channel);

            foreach ($adSlots as $adSlot) {
                /** @var DisplayAdSlotInterface|ReportableAdSlotInterface $adSlot */
                if (!$adSlot instanceof DisplayAdSlotInterface
                    || !$adSlot->isRTBEnabled()
                ) {
                    continue; // only supported DisplayAdSlot and rtbStatus of own site is inherited rtbStatus of this channel
                }

                $this->tagCache->refreshCacheForReportableAdSlot($adSlot, true);
            }
        }
    }
}