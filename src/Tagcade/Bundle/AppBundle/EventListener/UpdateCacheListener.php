<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Legacy\TagCacheInterface;

class UpdateCacheListener
{
    /**
     * @var TagCacheInterface
     */
    protected  $tagCache;

    function __construct(TagCacheInterface $tagCache)
    {
        $this->tagCache = $tagCache;
    }

    /**
     * @param UpdateCacheEvent $event
     */
    public function onUpdateCache(UpdateCacheEvent $event)
    {
        $this->tagCache->renewCacheForAdSlot($event->getAdSlot());
    }
}