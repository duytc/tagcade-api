<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Tagcade\Model\Core\ExchangeInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Worker\Manager;

/**
 * Class ExchangeChangeListener
 *
 * Handle event exchange changed for updating cache for "Display ad slot", "Ron ad slot"
 *
 * @package Tagcade\Bundle\AppBundle\EventListener
 */
class ExchangeChangeListener
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
     * Handle event onFlush
     *
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $this->changedEntities = array_merge($this->changedEntities, $uow->getScheduledEntityUpdates());

        $this->changedEntities = array_filter($this->changedEntities, function ($entity) {
            return $entity instanceof ExchangeInterface;
        });
    }

    /**
     * Handle event postFlush
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

        // filter all exchanges changed
        foreach ($this->changedEntities as $entity) {
            if (!$entity instanceof ExchangeInterface) {
                continue;
            }

            $changedFields = $uow->getEntityChangeSet($entity);

            if (array_key_exists('canonicalName', $changedFields)) {
                $needToBeUpdatedPublisherIds[] = array_merge($needToBeUpdatedPublisherIds, $entity->getPublisherIds());
            }
        }

        // update cache due to publisher
        if (count($needToBeUpdatedPublisherIds)) {
            $this->workerManager->updateCacheForPublishers($needToBeUpdatedPublisherIds);
        }

        // reset for new onFlush event
        $this->changedEntities = [];
    }
} 