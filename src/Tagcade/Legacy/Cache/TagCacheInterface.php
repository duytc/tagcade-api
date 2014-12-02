<?php

namespace Tagcade\Legacy;

use Tagcade\Model\Core\AdSlotInterface;

interface TagCacheInterface
{
    /**
     * @param AdSlotInterface $adSlotId
     * @return $this
     */
    public function renewCacheForAdSlot(AdSlotInterface $adSlotId);
    /**
     * @return $this
     */
    public function renewCache();
}