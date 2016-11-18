<?php

namespace Tagcade\Worker\Workers;

use StdClass;
use Tagcade\Cache\TagCacheManagerInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;


class RemoveCacheForAdSlotWorker
{
    /** @var TagCacheManagerInterface */
    private $tagCacheManager;

    /**
     * @var AdSlotManagerInterface
     */
    private $adSlotManager;

    function __construct(TagCacheManagerInterface $tagCacheManager, AdSlotManagerInterface $adSlotManager)
    {
        $this->tagCacheManager = $tagCacheManager;
        $this->adSlotManager = $adSlotManager;
    }

    /**
     * update cache for multiple channel ids
     *
     * @param StdClass $params
     */
    public function removeCacheForAdSlot(StdClass $params)
    {
        $adSlotId = $params->id;
        $adSlot = $this->adSlotManager->find($adSlotId);

        if ($adSlot instanceof BaseAdSlotInterface) {
            // do not remove cache of existing ad slot
            return;
        }

        $this->tagCacheManager->removeCacheForAdSlot($adSlotId);
    }
}