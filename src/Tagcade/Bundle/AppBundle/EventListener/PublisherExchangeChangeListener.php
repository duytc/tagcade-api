<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Model\Core\PublisherExchangeInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Worker\Manager;

/**
 * Class PublisherExchangeChangeListener
 *
 * Handle event publisher changed for updating cache for "Display ad slot" on fields relate to RTB-RealTime Bidding('rtbStatus', 'exchanges', ...)
 *
 * @package Tagcade\Bundle\AppBundle\EventListener
 */
class PublisherExchangeChangeListener
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
     * handle event preUpdate to detect publisher-exchange relation changed
     *
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof PublisherExchangeInterface && $args->hasChangedField('exchange')) {
            $this->changedEntities[] = $entity;
        }
    }

    /**
     * handle event prePersist to detect publisher-exchange relation created
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof PublisherExchangeInterface) {
            $this->changedEntities[] = $entity;
        }
    }

    /**
     * handle event postRemove to detect publisher-exchange relation removed
     *
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof PublisherExchangeInterface) {
            $this->changedEntities[] = $entity;
        }
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
            if (!$entity instanceof PublisherExchangeInterface) {
                continue;
            }

            $publisherId = $entity->getPublisher()->getId();

            // avoid duplicate publisher id
            if(!in_array($publisherId, $needToBeUpdatedPublisherIds)) {
                $needToBeUpdatedPublisherIds[] = $publisherId;
            }
        }

        // update cache due to publisher
        $this->workerManager->updateCacheForPublishers($needToBeUpdatedPublisherIds);

        // reset for new onFlush event
        $this->changedEntities = [];
    }
}