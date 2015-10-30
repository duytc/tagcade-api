<?php

namespace Tagcade\Bundle\AppBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryAdTag;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotSegmentInterface;
use Tagcade\Model\ModelInterface;

class RonAdSlotChangeListener {

    protected $updatingLibraryAdSlots = [];
    protected $updatingRonSlotSegments = [];

    function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof RonAdSlotInterface && !$entity instanceof LibraryExpressionInterface && !$entity instanceof LibrarySlotTagInterface) {
            return;
        }

        $libAdSlot = null;
        if ($entity instanceof RonAdSlotInterface) {
            $this->dispatchUpdateCacheEventDueToRonAdSlot($entity);
            return;
        }else if ($entity instanceof LibrarySlotTagInterface) {

            $libAdSlot = $entity->getLibraryAdSlot();
            if (!$libAdSlot->getLibSlotTags()->contains($entity)) {
                $libAdSlot->getLibSlotTags()->add($entity);
            }

        }
        else if ($entity instanceof LibraryExpressionInterface) {
            $libAdSlot =  $entity->getLibraryDynamicAdSlot();
        }

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

        if ($entity instanceof LibraryAdTagInterface && ($args->hasChangedField('html') || $args->hasChangedField('adType') || $args->hasChangedField('descriptor'))) {
            $libAdSlots = $this->getLibAdSlotsFromLibAdTag($entity);
            $this->updatingLibraryAdSlots = array_merge($this->updatingLibraryAdSlots, $libAdSlots);
        }
    }

    public function postSoftDelete(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $ronSlots = [];
        $ronSlots = array_merge($ronSlots, $this->getAffectedRonSlotFromUpdatingLibraryExpression($entity));
        $ronSlots = array_merge($ronSlots, $this->getAffectedRonSlotFromUpdatingLibrarySlotTag($entity));

        $this->eventDispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($ronSlots));
    }

    protected function getAffectedRonSlotFromUpdatingLibrarySlotTag(ModelInterface $entity)
    {
        if (!$entity instanceof LibrarySlotTagInterface) {
            return [];
        }

        $ronAdSlot = $entity->getLibraryAdSlot()->getRonAdSlot();

        return $ronAdSlot instanceof RonAdSlotInterface ? [$ronAdSlot] : [];
    }

    protected function getAffectedRonSlotFromUpdatingLibraryExpression(ModelInterface $entity)
    {
        if (!$entity instanceof LibraryExpressionInterface) {
            return [];
        }
        $libAdSlot =  $entity->getLibraryDynamicAdSlot();
        $ronAdSlot = $libAdSlot->getRonAdSlot();

        if (!$ronAdSlot instanceof RonAdSlotInterface) {
            return [];
        }

        return [$ronAdSlot];
    }

    protected function getLibAdSlotsFromLibAdTag(LibraryAdTagInterface $libAdTag) {
        $slotTags = $libAdTag->getLibSlotTags();
        if (null === $slotTags) {
            return [];
        }

        $libSlots = [];
        foreach ($slotTags as $slotTag) {
            /**
             * @var LibrarySlotTagInterface $slotTag
             */
            $libSlot = $slotTag->getLibraryAdSlot();
            if (!in_array($libSlot, $libSlots)) {
                $libSlots[] = $libSlot;
            }
        }

        return $libSlots;
    }

    /**
     * Handle deleteing multiple segments from ron slot
     * @param LifecycleEventArgs $args
     */
    public function preSoftDelete(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $this->updatingRonSlotSegments = array_merge(
            $this->updatingRonSlotSegments,
            array_filter(
                $uow->getScheduledEntityDeletions(),
                function($entity)
                {
                    return $entity instanceof RonAdSlotSegmentInterface;
                }
            )
        );

        $i = 0;
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $tmp = array_merge($uow->getScheduledEntityInsertions()); // handle inserting more segments to ron slot

        $tmpRonSlotSegments = array_filter($tmp, function($item) { return $item instanceof RonAdSlotSegmentInterface; });
        $this->updatingRonSlotSegments = array_merge($tmpRonSlotSegments, $this->updatingRonSlotSegments);

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

        $updatingRonSlotsFromSegment = array_map(
            function (RonAdSlotSegmentInterface $ronSlotSegment) {
                return $ronSlotSegment->getRonAdSlot();
            },
            $this->updatingRonSlotSegments
        );

        $this->updatingRonSlotSegments = [];

        foreach ($updatingRonSlotsFromSegment as $ronAdSlot) {
            if (!in_array($ronAdSlot, $ronAdSlots)) {
                $ronAdSlots[] = $ronAdSlot;
            }
        }

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