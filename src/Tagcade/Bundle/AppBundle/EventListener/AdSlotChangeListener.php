<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;

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

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->dispatchUpdateCacheEventDueToAdTag($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->dispatchUpdateCacheEventDueToAdTag($args);
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->dispatchUpdateCacheEventDueToAdTag($args);

        $this->dispatchUpdateCacheEventDueToAdSlot($args);
    }

    protected function dispatchUpdateCacheEventDueToAdTag(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof AdTagInterface) {
            $this->eventDispatcher->dispatch( new UpdateCacheEvent($entity->getAdSlot()));
        }
    }

    protected function dispatchUpdateCacheEventDueToAdSlot(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof AdSlotInterface) {
            $this->eventDispatcher->dispatch( new UpdateCacheEvent($entity));
        }
    }
} 