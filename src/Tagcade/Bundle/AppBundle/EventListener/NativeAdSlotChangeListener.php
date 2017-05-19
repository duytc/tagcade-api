<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Model\Core\NativeAdSlotInterface;

class NativeAdSlotChangeListener
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    private $updatedNativeAdSlots = null;

    function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof NativeAdSlotInterface) {
            return;
        }

        if ($entity instanceof NativeAdSlotInterface &&
            ($args->hasChangedField('autoRefresh')
                || $args->hasChangedField('refreshEvery')
                || $args->hasChangedField('maximumRefreshTimes')
            )
        ) {
            $this->updatedNativeAdSlots = array($entity);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof NativeAdSlotInterface) {
            return;
        }

        if (empty($this->updatedNativeAdSlots)) {
            return;
        }

        $adSlots = $this->updatedNativeAdSlots;
        if ($adSlots instanceof PersistentCollection) {
            $adSlots = $adSlots->toArray();
        }

        unset($this->updatedNativeAdSlots);

        $this->eventDispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($adSlots));
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->dispatchUpdateCacheEventDueToAdSlot($args);
    }

    protected function dispatchUpdateCacheEventDueToAdSlot(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof NativeAdSlotInterface) {
            return;
        }

        $this->eventDispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($entity));
    }
} 