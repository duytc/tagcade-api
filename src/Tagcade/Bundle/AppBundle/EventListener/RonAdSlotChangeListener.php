<?php

namespace Tagcade\Bundle\AppBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;

class RonAdSlotChangeListener {

    protected $updatingLibraryAdSlots = [];

    function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof RonAdSlotInterface && !$entity instanceof LibraryExpressionInterface) {
            return;
        }

        if ($entity instanceof RonAdSlotInterface) {
            $this->dispatchUpdateCacheEventDueToRonAdSlot($entity);
            return;
        }

        $libAdSlot =  $entity->getLibraryDynamicAdSlot();
        $ronAdSlot = $libAdSlot->getRonAdSlot();

        if (!$ronAdSlot instanceof RonAdSlotInterface) {
            return;
        }

        $this->dispatchUpdateCacheEventDueToRonAdSlot($ronAdSlot);
    }

    /**
     * handle event on pre-update. A Library Display or Native or Dynamic Ad Slot, which Ron Ad Slot using, is changed, so need find Ron Slot for updating cache
     * Notice, event of 'preUpdate' supports detecting changed fields by function '$args->hasChangedField()'
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if (
            (   // library display ad slot changed
                $entity instanceof LibraryDisplayAdSlotInterface
                && ($args->hasChangedField('width')
                    || $args->hasChangedField('height')
                    || $args->hasChangedField('autoFit')
                    || $args->hasChangedField('passbackMode'))
            )
            ||
            (   // library dynamic ad slot changed
                $entity instanceof LibraryDynamicAdSlotInterface
                && $args->hasChangedField('defaultLibraryAdSlot')
            )
        ) {
            if (!in_array($entity, $this->updatingLibraryAdSlots)) {
                $this->updatingLibraryAdSlots[] = $entity;
            }

            return;
        }

        if (
        (   // library expression of dynamic ad slot changed
            $entity instanceof LibraryExpressionInterface
            && ($args->hasChangedField('expressionDescriptor')
                || $args->hasChangedField('startingPosition')
                || $args->hasChangedField('expectLibraryAdSlot'))
        )
        ) {
            $libAdSlot =  $entity->getLibraryDynamicAdSlot();
            if (!in_array($libAdSlot, $this->updatingLibraryAdSlots)) {
                $this->updatingLibraryAdSlots[] = $libAdSlot;
            }

            return;
        }
    }

    public function postSoftDelete(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof LibraryExpressionInterface) {
            return;
        }

        $libAdSlot =  $entity->getLibraryDynamicAdSlot();
        $ronAdSlot = $libAdSlot->getRonAdSlot();

        if (!$ronAdSlot instanceof RonAdSlotInterface) {
            return;
        }

        $this->dispatchUpdateCacheEventDueToRonAdSlot($ronAdSlot);
    }

    /**
     * handle event on post update. A Library Display or Native or Dynamic Ad Slot, which Ron Ad Slot using, is updated (to database),
     * so need send event Ron Slot change for updating cache
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        $ronAdSlots = array_map(
            function(BaseLibraryAdSlotInterface $libAdSlot)
            {
                return $libAdSlot->getRonAdSlot();
            },
            $this->updatingLibraryAdSlots
        );

        $this->updatingLibraryAdSlots = [];

        $this->eventDispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($ronAdSlots));
    }

    /**
     * dispatch event on Ron Ad Slot created / changed directly
     * @param RonAdSlotInterface $ronAdSlot
     */
    protected function dispatchUpdateCacheEventDueToRonAdSlot(RonAdSlotInterface $ronAdSlot)
    {
        $this->eventDispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($ronAdSlot));
    }
} 