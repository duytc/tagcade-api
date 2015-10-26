<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;

class UpdateAdTagCountForAdNetworkListener
{
    const AD_TAG_UPDATE = 1;
    const AD_TAG_PERSIST = 2;
    const AD_TAG_DELETE = 3;

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

    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        $entities = array_merge($uow->getScheduledEntityUpdates());
        foreach ($entities as $entity) {
            if (!$entity instanceof AdTagInterface || ($entity instanceof AdTagInterface && null !== $entity->getDeletedAt())) {
                continue;
            }

            $this->increaseActiveAdTagCountForAdNetwork($entity, self::AD_TAG_UPDATE);

            $uow = $em->getUnitOfWork();
            $meta = $em->getClassMetadata(get_class($entity->getAdNetwork()));
            $uow->recomputeSingleEntityChangeSet($meta, $entity->getAdNetwork());
        }
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

        switch($tagOption) {
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
                }
                else {
                    $adNetwork->increasePausedAdTagsCount();
                    $adNetwork->decreaseActiveAdTagsCount();
                }
        }
    }
}