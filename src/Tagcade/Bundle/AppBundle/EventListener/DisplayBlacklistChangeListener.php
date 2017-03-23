<?php


namespace Tagcade\Bundle\AppBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Cache\V2\DisplayDomainListManagerInterface;
use Tagcade\Entity\Core\AdNetwork;
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
     * @var DisplayDomainListManagerInterface
     */
    protected $displayDomainListManager;

    /**
     * DisplayBlacklistChangeListener constructor.
     * @param Manager $workerManager
     * @param DisplayDomainListManagerInterface $displayDomainListManager
     */
    public function __construct(Manager $workerManager, DisplayDomainListManagerInterface $displayDomainListManager)
    {
        $this->workerManager = $workerManager;
        $this->displayDomainListManager = $displayDomainListManager;
        $this->adNetworks = [];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $entity = $args->getEntity();

        if (!$entity instanceof DisplayBlacklistInterface) {
            return;
        }

        if ($entity->isDefault()) {
            $adNetworkRepository = $em->getRepository(AdNetwork::class);
            $this->adNetworks = array_merge($this->adNetworks, $adNetworkRepository->getAdNetworksForPublisher($entity->getPublisher()));
        } else {
            $this->adNetworks = array_merge($this->adNetworks, $entity->getAdNetworks());
        }

        $this->displayDomainListManager->saveBlacklist($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $em = $args->getEntityManager();
        $entity = $args->getEntity();

        if (!$entity instanceof DisplayBlacklistInterface || (
                $entity instanceof DisplayBlacklistInterface && !(
                    $args->hasChangedField('domains') ||
                    $args->hasChangedField('isDefault')))
        ) {
            return;
        }

        $this->adNetworks = [];
        if ($args->hasChangedField('domains')) {
            if ($entity->isDefault()) {
                $adNetworkRepository = $em->getRepository(AdNetwork::class);
                $this->adNetworks = array_merge($this->adNetworks, $adNetworkRepository->getAdNetworksForPublisher($entity->getPublisher()));
            } else {
                $this->adNetworks = array_merge($this->adNetworks, $entity->getAdNetworks());
            }
            $this->displayDomainListManager->saveBlacklist($entity);
        }

        if ($args->hasChangedField('isDefault')) {
            $adNetworkRepository = $em->getRepository(AdNetwork::class);
            $this->adNetworks = array_merge($this->adNetworks, $adNetworkRepository->getAdNetworksForPublisher($entity->getPublisher()));
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        if (!$entity instanceof DisplayBlacklistInterface) {
            return;
        }

        if ($entity->isDefault()) {
            $adNetworkRepository = $em->getRepository(AdNetwork::class);
            $this->adNetworks = array_merge($this->adNetworks, $adNetworkRepository->getAdNetworksForPublisher($entity->getPublisher()));
        } else {
            $this->adNetworks = array_merge($this->adNetworks, $entity->getAdNetworks());
        }

        if ($entity->getId()) {
            $this->displayDomainListManager->delBlacklist($entity);
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