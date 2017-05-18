<?php


namespace Tagcade\Bundle\AppBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\NetworkBlacklistInterface;
use Tagcade\Model\Core\NetworkWhiteListInterface;
use Tagcade\Worker\Manager;

class NetworkWhiteListChangeListener
{
    /**
     * @var Manager
     */
    protected $workerManager;

    /**
     * @var NetworkWhiteListInterface[]
     */
    protected $networkWhiteLists;

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
        $this->networkWhiteLists = [];
        $this->oldAdNetworks = [];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof NetworkWhiteListInterface) {
            return;
        }

        $this->networkWhiteLists[] = $entity;
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof NetworkWhiteListInterface) {
            return;
        }
        $this->oldAdNetworks[] = $args->getOldValue('adNetwork');
        $this->networkWhiteLists[] = $entity;
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof NetworkWhiteListInterface) {
            return;
        }

        $this->networkWhiteLists[] = $entity;
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        if (count($this->networkWhiteLists) > 0) {
            $this->updateAdSlotCache($this->networkWhiteLists, $this->oldAdNetworks);
            $this->networkWhiteLists = [];
            $this->oldAdNetworks = [];
        }
    }

    /**
     * @param NetworkBlacklistInterface[] $netWorkWhiteLists
     * @param AdNetworkInterface[] $oldAdNetworks
     */
    private function updateAdSlotCache($netWorkWhiteLists, $oldAdNetworks)
    {
        $adNetworks = [];
        foreach ($netWorkWhiteLists as $netWorkWhiteList) {
            if ($netWorkWhiteList instanceof NetworkWhiteListInterface) {
                if (count($netWorkWhiteList->getDisplayWhiteList()->getAdNetworks()) != 0) {
                    $adNetworks = array_merge($adNetworks, $netWorkWhiteList->getDisplayWhiteList()->getAdNetworks());
                } else {
                    $adNetworks[] = $netWorkWhiteList->getAdNetwork();
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