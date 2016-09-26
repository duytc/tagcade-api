<?php

namespace Tagcade\Cache\V2\Refresher;


use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface RonAdSlotCacheInterface
{
    /**
     * refresh cache for a/all publisher(s)
     *
     * @param null|PublisherInterface $publisher null if refresh all publisher, default = null
     * @return mixed
     */
    public function refreshCache($publisher = null);

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param bool $alsoRefreshRelatedDynamicRonAdSlot
     * @return mixed
     */
    public function refreshCacheForRonAdSlot(RonAdSlotInterface $ronAdSlot, $alsoRefreshRelatedDynamicRonAdSlot = false);

    /**
     *
     * @param $ronAdSlotId
     * @return string|false jsons tring of ron slot tags data
     */
    public function getAdTagsForRonAdSlot($ronAdSlotId);

    public function getNamespace($ronSlotId);
}