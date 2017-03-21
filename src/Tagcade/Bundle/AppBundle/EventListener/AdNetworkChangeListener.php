<?php


namespace Tagcade\Bundle\AppBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Worker\Manager;

class AdNetworkChangeListener
{
    /**
     * @var Manager
     */
    protected $workerManager;

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

        if (count($entity->getNetworkBlacklists()) > 0){
            $this->workerManager->updateAdSlotCache($entity->getId());
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof AdNetworkInterface) {
            return;
        }

        if ($args->hasChangedField('networkBlacklists')) {
            $this->workerManager->updateAdSlotCache($entity->getId());
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof AdNetworkInterface) {
            return;
        }

        $this->workerManager->updateAdSlotCache($entity->getId());
    }
}