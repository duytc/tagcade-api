<?php

namespace Tagcade\Legacy;

use Doctrine\Common\Cache\Cache;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\Model\Core\AdSlotInterface;

class TagCache implements TagCacheInterface
{
    /**
     * @var AdSlotManagerInterface
     */
    private $adSlotManager;
    /**
     * @var NamespaceCacheInterface
     */
    private $namespaceCache;

    function __construct(AdSlotManagerInterface $adSlotManager, NamespaceCacheInterface $namespaceCache)
    {
        $this->adSlotManager = $adSlotManager;
        $this->namespaceCache = $namespaceCache;
    }

    public function renewCacheForAdSlot(AdSlotInterface $adSlotId)
    {
        // TODO: Implement setCacheForAdSlot() method.
    }

    public function renewCache()
    {
        $adSlots = $this->adSlotManager->all();

        foreach ($adSlots as $adSlot) {
            $this->renewCacheForAdSlot($adSlot);
        }
    }

} 