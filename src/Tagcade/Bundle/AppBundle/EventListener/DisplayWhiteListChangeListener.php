<?php


namespace Tagcade\Bundle\AppBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Cache\V2\DisplayWhiteListCacheManagerInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\DisplayWhiteListInterface;
use Tagcade\Worker\Manager;

class DisplayWhiteListChangeListener
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
     * @var DisplayWhiteListCacheManagerInterface
     */
    protected $displayWhiteListCacheManager;

    /**
     * DisplayBlacklistChangeListener constructor.
     * @param Manager $workerManager
     * @param DisplayWhiteListCacheManagerInterface $displayWhiteListCacheManager
     */
    public function __construct(Manager $workerManager, DisplayWhiteListCacheManagerInterface $displayWhiteListCacheManager)
    {
        $this->workerManager = $workerManager;
        $this->displayWhiteListCacheManager = $displayWhiteListCacheManager;
        $this->adNetworks = [];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof DisplayWhiteListInterface) {
            return;
        }

        $this->adNetworks = array_merge($this->adNetworks, $entity->getAdNetworks());
        $this->displayWhiteListCacheManager->saveWhiteList($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof DisplayWhiteListInterface ||
            ($entity instanceof DisplayWhiteListInterface && !$args->hasChangedField('domains'))
        ) {
            return;
        }

        $this->adNetworks = [];
        if ($args->hasChangedField('domains')) {
            $this->adNetworks = array_merge($this->adNetworks, $entity->getAdNetworks());
            $this->displayWhiteListCacheManager->saveWhiteList($entity);
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof DisplayWhiteListInterface) {
            return;
        }

        $this->adNetworks = array_merge($this->adNetworks, $entity->getAdNetworks());
        if ($entity->getId()) {
            $this->displayWhiteListCacheManager->deleteWhiteList($entity);
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