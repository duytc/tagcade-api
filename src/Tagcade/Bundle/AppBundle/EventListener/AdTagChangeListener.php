<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;

class AdTagChangeListener
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    protected $changedEntities;

    function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $tmp = array_merge($uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates(), $uow->getScheduledEntityDeletions());

        $this->changedEntities = $tmp;
    }

    // Truly refresh cache invocation
    public function postFlush(PostFlushEventArgs $args)
    {
        if (!isset($this->changedEntities) || !is_array($this->changedEntities) || count($this->changedEntities) < 1) {
            return;
        }

        $changedEntities = $this->changedEntities;

        unset($this->changedEntities);

       $adSlots = [];

        // filter all adTags and not (in $adSlots and in $adNetworks)
        array_walk($changedEntities,
            function($entity) use (&$adSlots)
            {
                if (!$entity instanceof AdTagInterface && !$entity instanceof LibraryAdTagInterface)
                {
                    return false;
                }

                $adTags = $entity instanceof LibraryAdTagInterface ? $entity->getAdTags(): [$entity];
                if (is_null($adTags)) { // ignore when update library with no tag reference
                    return false;
                }

                foreach($adTags as $tag) {
                    // ignore the ad tag in adSlot has been counted
                    if (in_array($tag->getAdSlot(), $adSlots)) {
                        continue;
                    }

                    $updatingAdSlot = $tag->getAdSlot();
                    if (!$updatingAdSlot->getAdTags()->contains($tag)) { // include the entity being inserted
                        $updatingAdSlot->getAdTags()->add($tag);
                    }

                    $adSlots[] = $updatingAdSlot;
                }


                return true;
            }
        );

        if (count($adSlots) > 0) {
            $this->eventDispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($adSlots));
        }

    }

} 