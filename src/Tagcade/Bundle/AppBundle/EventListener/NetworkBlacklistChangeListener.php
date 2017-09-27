<?php


namespace Tagcade\Bundle\AppBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\NetworkBlacklistInterface;
use Tagcade\Worker\Manager;

class NetworkBlacklistChangeListener
{
    /**
     * @var Manager
     */
    protected $workerManager;

    /**
     * @var NetworkBlacklistInterface[]
     */
    protected $networkBlacklists;

    /**
     * @var AdNetworkInterface[] $oldAdNetworks
     */
    protected $oldAdNetworks;

    /**
     * DisplayBlacklistChangeListener constructor.
     * @param Manager $workerManager
     */
    public function __construct(Manager $workerManager)
    {
        $this->workerManager = $workerManager;
        $this->networkBlacklists = [];
        $this->oldAdNetworks = [];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof NetworkBlacklistInterface) {
            return;
        }
        $this->networkBlacklists[] = $entity;
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof NetworkBlacklistInterface) {
            return;
        }

        if ($args->hasChangedField('adNetwork')) {
            $this->oldAdNetworks[] = $args->getOldValue('adNetwork');
        }

        $this->networkBlacklists[] = $entity;
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof NetworkBlacklistInterface) {
            return;
        }
        $this->networkBlacklists[] = $entity;
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        if (count($this->networkBlacklists) > 0) {
            $this->updateAdSlotCache($this->networkBlacklists, $this->oldAdNetworks);
            $this->networkBlacklists = [];
            $this->oldAdNetworks = [];
        }
    }

    /**
     * @param NetworkBlacklistInterface[] $netWorkBlacklists
     * @param AdNetworkInterface[] $oldAdNetworks
     */
    private function updateAdSlotCache($netWorkBlacklists, $oldAdNetworks)
    {
        $adNetworks = [];
        foreach ($netWorkBlacklists as $netWorkBlacklist) {
            if ($netWorkBlacklist instanceof NetworkBlacklistInterface) {
                if (count($netWorkBlacklist->getDisplayBlacklist()->getAdNetworks()) != 0) {
                    $adNetworks = array_merge($adNetworks, $netWorkBlacklist->getDisplayBlacklist()->getAdNetworks());
                } else {
                    $adNetworks[] = $netWorkBlacklist->getAdNetwork();
                }
            }
        }

        foreach ($oldAdNetworks as $oldAdNetwork) {
            if ($oldAdNetwork instanceof AdNetworkInterface) {
                $adNetworks[] = $oldAdNetwork;
            }
        }

        $adNetworks = array_unique($adNetworks);
        if ($adNetworks) {
            foreach ($adNetworks as $adNetwork) {
                if ($adNetwork instanceof AdNetworkInterface) {
                    $this->workerManager->updateAdSlotCacheForAdNetwork($adNetwork->getId());
                }
            }
        }
    }
}