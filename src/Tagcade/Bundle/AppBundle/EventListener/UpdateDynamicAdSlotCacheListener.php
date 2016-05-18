<?php

namespace Tagcade\Bundle\AppBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\ExpressionInterface;

class UpdateDynamicAdSlotCacheListener
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var array */
    protected $changedEntities = [];

    function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Keep tracking changes of dynamic ad slot then do actual refresh later with postFlush listener
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof DynamicAdSlotInterface || ($entity instanceof DynamicAdSlotInterface && !$args->hasChangedField('defaultAdSlot'))) {
            return;
        }

        $this->changedEntities[] = $entity;
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

        if ($entity->getExpressions()->isEmpty()) { // refresh cache when dynamic has no expression. If there is we rely on postFlush
            $this->refreshDynamicAdSlotCache($entity);
        }
    }

    /**
     * pickup all entities to be flushed here
     *
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $tmp = array_merge($uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates(), $uow->getScheduledEntityDeletions(), $this->changedEntities);

        $this->changedEntities = $tmp;
    }

    /**
     * Pick all dynamic ad slots and do actual refresh
     *
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        if (empty($this->changedEntities)) {
            return;
        }

        $changedEntities = $this->changedEntities;

        $this->changedEntities = []; // reset  changed entities track

        $adSlots = array_filter($changedEntities, function ($entity) {
                return $entity instanceof DynamicAdSlotInterface;
            });

        // filter all adTags and not (in $adSlots and in $adNetworks)
        array_walk($changedEntities,
            function ($entity) use (&$adSlots) {
                if (!$entity instanceof ExpressionInterface) {
                    return false;
                }

                $affectingDynamicAdSlot = $entity->getDynamicAdSlot();

                if (null === $entity->getDeletedAt() && !$affectingDynamicAdSlot->getExpressions()->contains($entity)) { // include the entity being inserted
                    $affectingDynamicAdSlot->getExpressions()->add($entity);
                } else if (null !== $entity->getDeletedAt()) { // remove expression
                    $removeElement = array_filter($affectingDynamicAdSlot->getExpressions()->toArray(), function (ExpressionInterface $e) use ($entity) {
                            return $e->getId() === $entity->getId();
                        });
                    $removeElement = current($removeElement);
                    if ($removeElement instanceof ExpressionInterface) {
                        $affectingDynamicAdSlot->getExpressions()->removeElement($removeElement);
                    }
                }

                if (!in_array($affectingDynamicAdSlot, $adSlots)) {
                    $adSlots[] = $affectingDynamicAdSlot;
                }

                return true;
            }
        );

        if (count($adSlots) > 0) {
            $this->eventDispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($adSlots));
        }

    }

    private function refreshDynamicAdSlotCache(DynamicAdSlotInterface $dynamicAdSlot)
    {
        $this->eventDispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($dynamicAdSlot));
    }
} 