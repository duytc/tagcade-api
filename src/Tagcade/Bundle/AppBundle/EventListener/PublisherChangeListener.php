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
 * Handle event publisher changed for updating cache for "Display ad slot" on fields relate to RTB-RealTime Bidding('rtbStatus', 'exchanges', ...)
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
     * Handle event onFlush for detecting publisher changed on fields relate to RTB (rtb, exchanges, ...), then update cache for display ad slot
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

            if (array_key_exists('roles', $changedFields)) { // the 'exchanges' field is already listened by SiteChangeListener!!!
                $roles = $changedFields['roles'];

                if (!is_array($roles) || count($roles) < 2) {
                    continue;
                }

                if ($this->hasModuleRTB($roles[0]) xor $this->hasModuleRTB($roles[1])) {
                    $needToBeUpdatedPublisherIds[] = $entity->getId();
                }
            }
        }

        // update cache due to publisher
        if (count($needToBeUpdatedPublisherIds)) {
            $this->workerManager->updateCacheForPublishers($needToBeUpdatedPublisherIds);
        }

        // reset for new onFlush event
        $this->changedEntities = [];
    }

    private function hasModuleRTB(array $roles)
    {
        return in_array('MODULE_RTB', $roles);
    }
} 