<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Worker\Manager;

/**
 * Class PublisherChangeListener
 *
 * Handle event publisher changed for updating cache for ad slot
 *
 * @package Tagcade\Bundle\AppBundle\EventListener
 */
class PublisherChangeListener
{
    /**
     * @var array|ModelInterface[]
     */
    protected $changedEntities = [];

    /** @var Manager */
    private $workerManager;

    function __construct(Manager $workerManager)
    {
        $this->workerManager = $workerManager;
    }

    /**
     * Handle event onFlush for detecting publisher changed, then update cache for display ad slot
     *
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $this->changedEntities = array_merge($this->changedEntities, $uow->getScheduledEntityUpdates());

        $this->changedEntities = array_filter($this->changedEntities, function ($entity) {
            return $entity instanceof PublisherInterface;
        });
    }

    /**
     * Handle event postFlush for building and dispatching cache event to update cache for all need-be-updated-AdSlots
     *
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        if (count($this->changedEntities) < 1) {
            return;
        }

        /** @var array|int[] $needToBeUpdatedPublisherIds */
        $needToBeUpdatedPublisherIds = [];
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        // filter all sites changed on rtb & exchanges, then build needBeUpdatedAdSlots
        foreach ($this->changedEntities as $entity) {
            if (!$entity instanceof PublisherInterface) {
                continue;
            }

            $changedFields = $uow->getEntityChangeSet($entity);

            //if (array_key_exists('<any key>', $changedFields)) {
            //    $needToBeUpdatedPublisherIds[] = $entity->getId();
            //    continue;
            //}
        }

        // update cache due to publisher
        if (count($needToBeUpdatedPublisherIds)) {
            $this->workerManager->updateCacheForPublishers($needToBeUpdatedPublisherIds);
        }

        // reset for new onFlush event
        $this->changedEntities = [];
    }
}