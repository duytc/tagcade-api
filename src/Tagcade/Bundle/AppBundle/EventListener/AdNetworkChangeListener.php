<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Model\Core\AdNetworkInterface;

class AdNetworkChangeListener
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
        $entity = $args->getEntity();

        if (!$entity instanceof AdNetworkInterface) {
            return;
        }

        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $changeSet = $uow->getEntityChangeSet($entity);

        if (true === array_key_exists('active', $changeSet)) {
            $this->dispatchUpdateCacheEventDueToAdNetwork($args);
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->dispatchUpdateCacheEventDueToAdNetwork($args);
    }

    protected function dispatchUpdateCacheEventDueToAdNetwork(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof AdNetworkInterface) {
            return;
        }

        $this->eventDispatcher->dispatch(new UpdateCacheEvent($entity));
    }
} 