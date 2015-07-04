<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Model\Core\DisplayAdSlotInterface;

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

//    public function postUpdate(LifecycleEventArgs $args)
//    {
//        $entity = $args->getEntity();
//
//        if (!$entity instanceof AdSlotInterface) {
//            return;
//        }
//
//        $em = $args->getEntityManager();
//        $uow = $em->getUnitOfWork();
//
//        $changeSet = $uow->getEntityChangeSet($entity);
//
//        if (true === array_key_exists('variableDescriptor', $changeSet) || true === array_key_exists('enableVariable', $changeSet)) {
//            $this->dispatchUpdateCacheEventDueToAdSlot($args);
//        }
//    }

//    public function postRemove(LifecycleEventArgs $args)
//    {
//        $this->dispatchUpdateCacheEventDueToAdSlot($args);
//    }

    protected function dispatchUpdateCacheEventDueToAdSlot(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof DisplayAdSlotInterface) {
            return;
        }

        $this->eventDispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($entity));
    }
} 