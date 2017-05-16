<?php


namespace Tagcade\Bundle\AppBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Cache\V2\DisplayBlacklistCacheManagerInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\DisplayBlacklistInterface;
use Tagcade\Worker\Manager;

class DisplayBlacklistChangeListener
{
    /**
     * @var Manager
     */
    protected $workerManager;

    /**
     * @var AdNetworkInterface[]
     */
    protected $adNetworks;

    /**
     * @var DisplayBlacklistCacheManagerInterface
     */
    protected $displayBlacklistCacheManager;

    /**
     * DisplayBlacklistChangeListener constructor.
     * @param Manager $workerManager
     * @param DisplayBlacklistCacheManagerInterface $displayBlacklistCacheManager
     */
    public function __construct(Manager $workerManager, DisplayBlacklistCacheManagerInterface $displayBlacklistCacheManager)
    {
        $this->workerManager = $workerManager;
        $this->displayBlacklistCacheManager = $displayBlacklistCacheManager;
        $this->adNetworks = [];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof DisplayBlacklistInterface) {
            return;
        }

        $this->adNetworks = array_merge($this->adNetworks, $entity->getAdNetworks());
        $this->displayBlacklistCacheManager->saveBlacklist($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof DisplayBlacklistInterface ||
            ($entity instanceof DisplayBlacklistInterface && !$args->hasChangedField('domains'))
        ) {
            return;
        }

        $this->adNetworks = [];
        if ($args->hasChangedField('domains')) {
            $this->adNetworks = array_merge($this->adNetworks, $entity->getAdNetworks());
            $this->displayBlacklistCacheManager->saveBlacklist($entity);
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof DisplayBlacklistInterface) {
            return;
        }

        $this->adNetworks = array_merge($this->adNetworks, $entity->getAdNetworks());
        if ($entity->getId()) {
            $this->displayBlacklistCacheManager->deleteBlacklist($entity);
        }
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        if (count($this->adNetworks) > 0) {
            $this->updateAdSlotCache($this->adNetworks);
            $this->adNetworks = [];
        }
    }

    /**
     * @param AdNetworkInterface[] $adNetworks
     */
    private function updateAdSlotCache($adNetworks)
    {
        $adNetworks = array_unique($adNetworks);
        if ($adNetworks) {
            foreach ($adNetworks as $adNetwork) {
                if ($adNetwork instanceof AdNetworkInterface) {
                    $this->workerManager->updateAdSlotCache($adNetwork->getId());
                }
            }
        }
    }
}