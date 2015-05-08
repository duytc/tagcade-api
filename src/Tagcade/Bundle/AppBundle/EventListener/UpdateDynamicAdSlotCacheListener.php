<?php

namespace Tagcade\Bundle\AppBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Cache\DynamicAdSlot\TagCache;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\ExpressionInterface;

class UpdateDynamicAdSlotCacheListener
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var array
     */
    protected $changedEntities;

    function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Update cache on new DynamicAdSlotInterface
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof DynamicAdSlotInterface) {
            return;
        }

        $this->refreshDynamicAdSlotCache($entity);
    }

    /**
     * keep persisting ExpressionInterface[], and then do Cache refresh in postFlush for DynamicAdSlot that containing these ExpressionInterface[]
     * scheduled to remove expressions is not included
     *
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $tmp = array_merge($uow->getScheduledEntityInsertions());

        $this->changedEntities = $tmp;
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if (!isset($this->changedEntities) || !is_array($this->changedEntities) || count($this->changedEntities) < 1) {
            return;
        }

        $changedEntities = $this->changedEntities;

        unset($this->changedEntities);

        $dynamicAdSlots = [];

        // filter all adTags and not (in $adSlots and in $adNetworks)
        array_walk($changedEntities,
            function($entity) use (&$dynamicAdSlots)
            {
                if ($entity instanceof DynamicAdSlotInterface && !in_array($entity, $dynamicAdSlots)) {
                    $dynamicAdSlots[] = $entity;
                }

                if (!$entity instanceof ExpressionInterface)
                {
                    return false;
                }

                $updatingDynamicAdSlot = $entity->getDynamicAdSlot();
                // ignore the ad tag in adSlot has been counted
                if (in_array($updatingDynamicAdSlot, $dynamicAdSlots)) {
                    return false;
                }

                $dynamicAdSlots[] = $updatingDynamicAdSlot;

                return true;
            }
        );

        if (count($dynamicAdSlots) > 0) {
            $this->eventDispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($dynamicAdSlots));
        }

    }

    private function refreshDynamicAdSlotCache(DynamicAdSlotInterface $dynamicAdSlot)
    {
        $this->eventDispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($dynamicAdSlot));
    }
} 