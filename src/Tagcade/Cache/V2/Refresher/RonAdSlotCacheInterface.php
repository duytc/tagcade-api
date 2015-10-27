<?php

namespace Tagcade\Cache\V2\Refresher;


use Tagcade\Model\Core\RonAdSlotInterface;

interface RonAdSlotCacheInterface {
    /**
     * public api, refresh Cache of all ad slot (display, native, dynamic, ron ad slot)
     * @return mixed
     */
    public function refreshCache();

    public function refreshCacheForRonAdSlot(RonAdSlotInterface $ronAdSlot, $alsoRefreshRelatedDynamicRonAdSlot = false);

} 