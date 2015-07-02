<?php

namespace Tagcade\Cache;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;

interface TagCacheInterface
{
    /**
     * @param DisplayAdSlotInterface $adSlot
     * @return $this
     */
    public function refreshCacheForDisplayAdSlot(DisplayAdSlotInterface $adSlot);

    /**
     * @param AdNetworkInterface $adNetwork
     * @return $this
     */
    public function refreshCacheForAdNetwork(AdNetworkInterface $adNetwork);

    /**
     * @return $this
     */
    public function refreshCache();

    public function supportVersion($version);
}