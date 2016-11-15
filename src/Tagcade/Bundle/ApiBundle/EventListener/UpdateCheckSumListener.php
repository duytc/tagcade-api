<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;

class UpdateCheckSumListener
{
    private $changedAdTags = [];
    private $changedAdSlots = [];

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof LibraryAdTagInterface && $args->hasChangedField('name')) {
            foreach($entity->getAdTags() as $adTag) {
                $this->changedAdTags[$adTag->getId()] = $adTag;
            }

            return;
        }

        if ($entity instanceof AdTagInterface && ($args->hasChangedField('position') ||
                $args->hasChangedField('active') ||
                $args->hasChangedField('refId') ||
                $args->hasChangedField('rotation') ||
                $args->hasChangedField('frequencyCap') ||
                $args->hasChangedField('libraryAdTag')
            )
        ) {
            $entity->setCheckSum();
            $this->changedAdSlots[$entity->getAdSlot()->getId()] = $entity->getAdSlot();
            return;
        }

        if ($entity instanceof AdTagInterface && $args->hasChangedField('checkSum')
        ) {
            $this->changedAdSlots[$entity->getAdSlot()->getId()] = $entity->getAdSlot();
            return;
        }

        if ($entity instanceof ReportableAdSlotInterface && $args->hasChangedField('libraryAdSlot')) {
            $entity->setCheckSum();
            return;
        }
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof AdTagInterface || $entity instanceof ReportableAdSlotInterface) {
            $entity->setCheckSum();
        }
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        $em = $args->getEntityManager();

        if (count($this->changedAdSlots) < 1 && count($this->changedAdTags) < 1) {
            return;
        }

        foreach($this->changedAdTags as $adTag) {
            if (!$adTag instanceof AdTagInterface) {
                continue;
            }

            $adTag->setCheckSum();
            $em->merge($adTag);
        }

        foreach($this->changedAdSlots as $adSlot) {
            if (!$adSlot instanceof ReportableAdSlotInterface) {
                continue;
            }

            $adSlot->setCheckSum();
            $em->merge($adSlot);
        }

        $this->changedAdTags = [];
        $this->changedAdSlots = [];
        $em->flush();
    }
}
