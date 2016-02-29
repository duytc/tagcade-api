<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;

class AdSlotChangeListener
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    private $updatedAdSlots = null;

    function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->dispatchUpdateCacheEventDueToAdSlot($args);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof DisplayAdSlotInterface && !$entity instanceof LibraryDisplayAdSlotInterface) {
            return;
        }

        if ($entity instanceof DisplayAdSlotInterface &&
            ($args->hasChangedField('rtbStatus') || $args->hasChangedField('floorPrice'))
        ) {
            $this->updatedAdSlots = array($entity);
        }

        if ($entity instanceof LibraryDisplayAdSlotInterface &&
            ($args->hasChangedField('width') || $args->hasChangedField('height') || $args->hasChangedField('autoFit') || $args->hasChangedField('passbackMode'))
        ) {
            $this->updatedAdSlots = $entity->getAdSlots();
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof DisplayAdSlotInterface && !$entity instanceof LibraryDisplayAdSlotInterface) {
            return;
        }

        if (empty($this->updatedAdSlots)) {
            return;
        }

        $adSlots = $this->updatedAdSlots;
        if($adSlots instanceof PersistentCollection) {
            $adSlots = $adSlots->toArray();
        }

        unset($this->updatedAdSlots);

        $this->eventDispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($adSlots));

    }

    protected function dispatchUpdateCacheEventDueToAdSlot(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof DisplayAdSlotInterface) {
            return;
        }

        $this->eventDispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($entity));
    }
} 