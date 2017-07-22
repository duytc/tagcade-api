<?php


namespace Tagcade\Bundle\AppBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Worker\Manager;

class AdNetworkChangeListener
{
    /**
     * @var Manager
     */
    protected $workerManager;

    protected $changedAdNetworks = [];

    /**
     * DisplayBlacklistChangeListener constructor.
     * @param Manager $workerManager
     */
    public function __construct(Manager $workerManager)
    {
        $this->workerManager = $workerManager;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof AdNetworkInterface) {
            return;
        }

        if (count($entity->getNetworkBlacklists()) > 0 || count($entity->getCustomImpressionPixels()) > 0) {
            $this->changedAdNetworks[] = $entity;
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof AdNetworkInterface) {
            return;
        }

        if ($args->hasChangedField('customImpressionPixels')) {
            $this->changedAdNetworks[] = $entity;
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof AdNetworkInterface) {
            return;
        }

        $this->changedAdNetworks[] = $entity;
    }

    /**
     * Handle event postFlush for building and dispatching cache event to update cache for all need-be-updated-AdSlots
     *
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        if (count($this->changedAdNetworks) < 1) {
            return;
        }

        // filter all ad networks changed, then build needBeUpdatedAdSlots
        foreach ($this->changedAdNetworks as $adNetwork) {
            if (!$adNetwork instanceof AdNetworkInterface) {
                continue;
            }

            $this->workerManager->updateAdSlotCacheForAdNetwork($adNetwork->getId());
        }

        // reset for new onFlush event
        $this->changedAdNetworks = [];
    }
}