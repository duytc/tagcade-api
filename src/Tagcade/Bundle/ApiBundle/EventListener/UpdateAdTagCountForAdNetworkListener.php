<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Repository\Core\AdTagRepositoryInterface;

class UpdateAdTagCountForAdNetworkListener
{
    /** @var array|AdNetworkInterface[] */
    private $changedAdNetworks = [];

    /** @var AdTagRepositoryInterface $adTagRepository */
    private $adTagRepository;

    /**
     * handle event prePersist to detect new ad tag is added, used for updating number of active|paused ad tags of ad network
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof LibraryAdTagInterface && !$entity instanceof AdTagInterface) {
            return;
        }

        $this->changedAdNetworks[] = $entity->getAdNetwork();
    }

    /**
     * handle event preUpdate to detect new ad tag is updated, used for updating number of active|paused ad tags of ad network
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof LibraryAdTagInterface && !$entity instanceof AdTagInterface) {
            return;
        }

        if ($args->hasChangedField('adNetwork')) {
            $this->changedAdNetworks[] = $args->getOldValue('adNetwork');
            $this->changedAdNetworks[] = $args->getNewValue('adNetwork');
        } else if ($args->hasChangedField('active')) {
            $this->changedAdNetworks[] = $entity->getAdNetwork();
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof LibraryAdTagInterface && !$entity instanceof AdTagInterface) {
            return;
        }

        $this->changedAdNetworks[] = $entity->getAdNetwork();
    }

    /**
     * handle event postFlush to update number of active|paused ad tags of ad network for all recorded changedAdNetworks
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        if (count($this->changedAdNetworks) < 1) {
            return;
        }

        $em = $args->getEntityManager();
        $this->adTagRepository = $em->getRepository(AdTag::class);

        $adNetworks = $this->changedAdNetworks;
        $this->changedAdNetworks = [];
        $realAdNetwork = [];
        $count = 0;

        foreach ($adNetworks as $adNetwork) {
            if (!$adNetwork instanceof AdNetworkInterface) {
                continue;
            }

            $realAdNetwork[$adNetwork->getId()] = $adNetwork;
        }

        foreach ($realAdNetwork as $adNetwork) {
            if (!$adNetwork instanceof AdNetworkInterface) {
                continue;
            }

            $adNetwork = $this->countActiveAndPausedAdTagsForAdNetwork($adNetwork);

            $em->merge($adNetwork);
            $count++;
        }

        if ($count > 0) {
            $em->flush();
        }
    }

    /**
     * @param AdNetworkInterface $adNetwork
     * @return AdNetworkInterface
     */
    private function countActiveAndPausedAdTagsForAdNetwork(AdNetworkInterface $adNetwork)
    {
        $countActiveAdTags = $this->adTagRepository->getAdTagsCountForAdNetworkByStatus($adNetwork, [AdTagInterface::ACTIVE]);
        $countPausedAdTags = $this->adTagRepository->getAdTagsCountForAdNetworkByStatus($adNetwork, [AdTagInterface::PAUSED, AdTagInterface::AUTO_PAUSED]);

        $adNetwork->setActiveAdTagsCount((int)$countActiveAdTags);
        $adNetwork->setPausedAdTagsCount((int)$countPausedAdTags);

        return $adNetwork;
    }
}