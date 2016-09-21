<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;

class UpdateDemandAdTagCountForVideoDemandPartnerListener
{
    const DEMAND_AD_TAG_UPDATE = 1;
    const DEMAND_AD_TAG_PERSIST = 2;
    const DEMAND_AD_TAG_DELETE = 3;

    /** @var array|VideoDemandPartnerInterface[] */
    private $changedDemandPartners = [];
    private $changedDemandPartnerIds = [];

    /**
     * handle event prePersist to detect new ad tag is added, used for updating number of active|paused ad tags of ad network
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // only listen Ad Tag instance
        if (!$entity instanceof VideoDemandAdTagInterface) {
            return;
        }

        $this->increaseActiveAdTagCountForVideoDemandPartner($entity, self::DEMAND_AD_TAG_PERSIST);
    }

    /**
     * handle event preUpdate to detect new ad tag is updated, used for updating number of active|paused ad tags of ad network
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof LibraryVideoDemandAdTagInterface && $args->hasChangedField('videoDemandPartner')) {
            $demandAdTags = $entity->getVideoDemandAdTags();

            /** @var VideoDemandPartnerInterface $oldDemandPartner , $newDemandPartner */
            $oldDemandPartner = $args->getOldValue('videoDemandPartner');

            /** @var VideoDemandPartnerInterface $newDemandPartner */
            $newDemandPartner = $args->getNewValue('videoDemandPartner');

            /** @var VideoDemandAdTagInterface $demandAdTag */
            foreach ($demandAdTags as $demandAdTag) {
                $demandAdTag->getActive() ? $oldDemandPartner->decreaseActiveAdTagsCount() : $oldDemandPartner->decreasePausedAdTagsCount();
                $demandAdTag->getActive() ? $newDemandPartner->increaseActiveAdTagsCount() : $newDemandPartner->increasePausedAdTagsCount();

                if (!in_array($oldDemandPartner->getId(), $this->changedDemandPartnerIds)) {
                    $this->changedDemandPartners[] = $oldDemandPartner;
                    $this->changedDemandPartnerIds[] = $oldDemandPartner->getId();
                }

                if (!in_array($newDemandPartner->getId(), $this->changedDemandPartnerIds)) {
                    $this->changedDemandPartners[] = $newDemandPartner;
                    $this->changedDemandPartnerIds[] = $newDemandPartner->getId();
                }
            }
        } else if ($entity instanceof VideoDemandAdTagInterface && $args->hasChangedField('active') && $args->getNewValue('active') !== null) {
            $active = filter_var($args->getNewValue('active'), FILTER_VALIDATE_BOOLEAN);

            $demandPartner = $entity->getVideoDemandPartner();
            if (!$demandPartner instanceof VideoDemandPartnerInterface) {
                die(sprintf('ad tag %d does not belong to any demand partner', $entity->getId()));
            }
            if ($demandPartner instanceof VideoDemandPartnerInterface) {
                if ($active === true) {
                    $entity->getVideoDemandPartner()->increaseActiveAdTagsCount();
                    $entity->getVideoDemandPartner()->decreasePausedAdTagsCount();
                } else {
                    $entity->getVideoDemandPartner()->increasePausedAdTagsCount();
                    $entity->getVideoDemandPartner()->decreaseActiveAdTagsCount();
                }

                if (!in_array($demandPartner->getId(), $this->changedDemandPartnerIds)) {
                    $this->changedDemandPartners[] = $demandPartner;
                    $this->changedDemandPartnerIds[] = $demandPartner->getId();
                }
            }
        }
    }

    /**
     * handle event postFlush to update number of active|paused ad tags of ad network for all recorded changedAdNetworks
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        if (count($this->changedDemandPartners) < 1) {
            return;
        }

        $em = $args->getEntityManager();
        foreach ($this->changedDemandPartners as $demandPartner) {
            $em->merge($demandPartner);
        }

        $this->changedDemandPartnerIds = [];
        $this->changedDemandPartners = [];
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
        if (!$entity instanceof VideoDemandAdTagInterface) {
            return;
        }

        $this->increaseActiveAdTagCountForVideoDemandPartner($entity, self::DEMAND_AD_TAG_DELETE);
    }

    protected function increaseActiveAdTagCountForVideoDemandPartner(VideoDemandAdTagInterface &$demandAdTag, $tagOption = self::DEMAND_AD_TAG_PERSIST)
    {
        $demandPartner = $demandAdTag->getVideoDemandPartner();
        if (!$demandPartner instanceof VideoDemandPartnerInterface) {
            return;
        }

        switch ($tagOption) {
            case self::DEMAND_AD_TAG_PERSIST:
                $demandAdTag->getActive() ? $demandPartner->increaseActiveAdTagsCount() : $demandPartner->increasePausedAdTagsCount();

                break;
            case self::DEMAND_AD_TAG_DELETE:
                $demandAdTag->getActive() ? $demandPartner->decreaseActiveAdTagsCount() : $demandPartner->decreasePausedAdTagsCount();

                break;
            case self::DEMAND_AD_TAG_UPDATE:
                if ($demandAdTag->getActive()) {
                    $demandPartner->increaseActiveAdTagsCount();
                    $demandPartner->decreasePausedAdTagsCount();
                } else {
                    $demandPartner->increasePausedAdTagsCount();
                    $demandPartner->decreaseActiveAdTagsCount();
                }
        }
    }
}