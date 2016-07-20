<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;

class UpdateAdTagCountForAdNetworkListener
{
    const AD_TAG_UPDATE = 1;
    const AD_TAG_PERSIST = 2;
    const AD_TAG_DELETE = 3;

    /** @var array|AdNetworkInterface[] */
    private $changedAdNetworks = [];

    /**
     * handle event prePersist to detect new ad tag is added, used for updating number of active|paused ad tags of ad network
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // only listen Ad Tag instance
        if (!$entity instanceof AdTagInterface) {
            return;
        }

        $this->increaseActiveAdTagCountForAdNetwork($entity, self::AD_TAG_PERSIST);
    }

    /**
     * handle event preUpdate to detect new ad tag is updated, used for updating number of active|paused ad tags of ad network
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof LibraryAdTagInterface && $args->hasChangedField('adNetwork')) {
            $adTags = $entity->getAdTags();

            /** @var AdNetworkInterface $oldAdNetwork , $newAdNetwork */
            $oldAdNetwork = $args->getOldValue('adNetwork');

            /** @var AdNetworkInterface $newAdNetwork */
            $newAdNetwork = $args->getNewValue('adNetwork');

            /** @var AdTagInterface $adTag */
            foreach ($adTags as $adTag) {
                $adTag->isActive() ? $oldAdNetwork->decreaseActiveAdTagsCount() : $oldAdNetwork->decreasePausedAdTagsCount();
                $adTag->isActive() ? $newAdNetwork->increaseActiveAdTagsCount() : $newAdNetwork->increasePausedAdTagsCount();

                $this->changedAdNetworks[] = $oldAdNetwork;
                $this->changedAdNetworks[] = $newAdNetwork;
            }
        } else if ($entity instanceof AdTagInterface && $args->hasChangedField('active') && $args->getNewValue('active') !== null) {
            $active = filter_var($args->getNewValue('active'), FILTER_VALIDATE_BOOLEAN);

            $adNetwork = $entity->getAdNetwork();
            if (!$adNetwork instanceof AdNetworkInterface) {
                die(sprintf('ad tag %d does not belong to any ad network', $entity->getId()));
            }
            if ($adNetwork instanceof AdNetworkInterface) {
                if ($active === true) {
                    $entity->getAdNetwork()->increaseActiveAdTagsCount();
                    $entity->getAdNetwork()->decreasePausedAdTagsCount();
                } else {
                    $entity->getAdNetwork()->increasePausedAdTagsCount();
                    $entity->getAdNetwork()->decreaseActiveAdTagsCount();
                }

                $this->changedAdNetworks[] = $adNetwork;
            }
        }
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
        foreach ($this->changedAdNetworks as $adNetwork) {
            $em->merge($adNetwork);
        }

        $this->changedAdNetworks = [];
        $em->flush();
    }

    /**
     * handle event prePersist to detect new ad tag is added, used for updating number of active|paused ad tags of ad network
     * @param LifecycleEventArgs $args
     */
    public function preSoftDelete(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // only listen Ad Tag instance
        if (!$entity instanceof AdTagInterface) {
            return;
        }

        $this->increaseActiveAdTagCountForAdNetwork($entity, self::AD_TAG_DELETE);
    }

    protected function increaseActiveAdTagCountForAdNetwork(AdTagInterface &$adTag, $tagOption = self::AD_TAG_PERSIST)
    {
        $adNetwork = $adTag->getAdNetwork();
        if (!$adNetwork instanceof AdNetworkInterface) {
            return;
        }

        switch ($tagOption) {
            case self::AD_TAG_PERSIST:
                $adTag->isActive() ? $adNetwork->increaseActiveAdTagsCount() : $adNetwork->increasePausedAdTagsCount();

                break;
            case self::AD_TAG_DELETE:
                $adTag->isActive() ? $adNetwork->decreaseActiveAdTagsCount() : $adNetwork->decreasePausedAdTagsCount();

                break;
            case self::AD_TAG_UPDATE:
                if ($adTag->isActive()) {
                    $adNetwork->increaseActiveAdTagsCount();
                    $adNetwork->decreasePausedAdTagsCount();
                } else {
                    $adNetwork->increasePausedAdTagsCount();
                    $adNetwork->decreaseActiveAdTagsCount();
                }
        }
    }
}