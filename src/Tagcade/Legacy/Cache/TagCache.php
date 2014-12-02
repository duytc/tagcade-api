<?php

namespace Tagcade\Legacy;

use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\Model\Core\AdSlotInterface;

class TagCache implements TagCacheInterface
{
    /**
     * @var AdSlotManagerInterface
     */
    private $adSlotManager;

    function __construct(AdSlotManagerInterface $adSlotManager)
    {
        $this->adSlotManager = $adSlotManager;
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