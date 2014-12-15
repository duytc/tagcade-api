<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Legacy\TagCacheInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdSlotInterface;

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
        $entity = $event->getEntity();

        if ($entity instanceof AdSlotInterface) {
            $this->tagCache->refreshCacheForAdSlot($entity);
        }
        else if ($entity instanceof AdNetworkInterface) {
            $this->tagCache->refreshCacheForAdNetwork($entity);
        }

    }
}