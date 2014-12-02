<?php

namespace Tagcade\Legacy\Util;

interface TagCacheInterface
{
    /**
     * @param $slotId
     * @param array $tagDataArray
     * @return $this
     */
    public function setCacheForAdSlot($slotId, array $tagDataArray);

    /**
     * @return $this
     */
    public function refreshCache();
}