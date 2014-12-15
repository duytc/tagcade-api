<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Model\Core\AdTagInterface;

class AdTagChangeListener
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
        $this->dispatchUpdateCacheEventDueToAdTag($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->dispatchUpdateCacheEventDueToAdTag($args);
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->dispatchUpdateCacheEventDueToAdTag($args);
    }

    protected function dispatchUpdateCacheEventDueToAdTag(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof AdTagInterface) {
            return;
        }

        $this->eventDispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($entity->getAdSlot()));
    }
} 