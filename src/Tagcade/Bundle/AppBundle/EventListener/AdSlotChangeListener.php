<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Model\Core\AdSlotInterface;

class AdSlotChangeListener
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->dispatchUpdateCacheEventDueToAdSlot($args);
    }

//    public function postRemove(LifecycleEventArgs $args)
//    {
//        $this->dispatchUpdateCacheEventDueToAdSlot($args);
//    }

    protected function dispatchUpdateCacheEventDueToAdSlot(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof AdSlotInterface) {
            return;
        }

        $this->eventDispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($entity));
    }
} 