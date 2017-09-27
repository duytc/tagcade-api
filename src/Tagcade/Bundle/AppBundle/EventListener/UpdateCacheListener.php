<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Cache\TagCacheManagerInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Worker\Manager;

class UpdateCacheListener
{
    /** @var TagCacheManagerInterface */
    protected $tagCacheManager;

    /**
     * @var array
     */
    protected $changedEntities;

    /** @var Manager  */
    protected $workerManager;

    function __construct(TagCacheManagerInterface $tagCacheManager, Manager $workerManager)
    {
        $this->tagCacheManager = $tagCacheManager;
        $this->workerManager = $workerManager;
    }

    /**
     * @param UpdateCacheEvent $event
     */
    public function onUpdateCache(UpdateCacheEvent $event)
    {
        $entities = $event->getEntities();

        array_walk($entities, function (ModelInterface $entity) {
                if ($entity instanceof BaseAdSlotInterface) {
//                    $this->tagCacheManager->refreshCacheForAdSlot($entity);
                    $this->workerManager->updateCacheForAdSlot($entity);
                } else if ($entity instanceof AdNetworkInterface) {
                    $this->tagCacheManager->refreshCacheForAdNetwork($entity);
                } else if ($entity instanceof RonAdSlotInterface) {
                    $this->tagCacheManager->refreshCacheForRonAdSlot($entity);
                }
            }
        );
    }
}