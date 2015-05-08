<?php

namespace Tagcade\Cache;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdSlotInterface;

interface TagCacheInterface
{
    /**
     * @param AdSlotInterface $adSlot
     * @return $this
     */
    public function refreshCacheForAdSlot(AdSlotInterface $adSlot);

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