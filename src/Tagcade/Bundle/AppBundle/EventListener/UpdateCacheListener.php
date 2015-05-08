<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Cache\TagCacheManagerInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\ModelInterface;

class UpdateCacheListener
{
    /**
     * @var TagCacheManagerInterface
     */
    protected $tagCacheManager;

    /**
     * @var array
     */
    protected $changedEntities;

    function __construct(TagCacheManagerInterface $tagCacheManager)
    {
        $this->tagCacheManager = $tagCacheManager;
    }

    /**
     * @param UpdateCacheEvent $event
     */
    public function onUpdateCache(UpdateCacheEvent $event)
    {
        $entities = $event->getEntities();

        array_walk($entities, function(ModelInterface $entity) {
                if ($entity instanceof AdSlotInterface) {
                    $this->tagCacheManager->refreshCacheForAdSlot($entity);
                }
                else if ($entity instanceof AdNetworkInterface) {
                    $this->tagCacheManager->refreshCacheForAdNetwork($entity);
                }
                else if ($entity instanceof DynamicAdSlotInterface) {
                    $this->tagCacheManager->refreshCacheForDynamicAdSlot($entity);
                }
            }
        );
    }



}