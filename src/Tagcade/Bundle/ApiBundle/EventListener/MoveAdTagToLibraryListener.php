<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;

use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\PersistentCollection;
use Tagcade\Entity\Core\LibrarySlotTag;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;

/**
 * This listener will be triggered when an ad slot is moved to library. The listener will then update all tags in side that slot to library
 * Class MoveAdTagToLibraryListener
 * @package Tagcade\Bundle\ApiBundle\EventListener
 */
class MoveAdTagToLibraryListener
{
    private $newSlotTags = array();

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof LibraryDisplayAdSlotInterface && !$entity instanceof LibraryNativeAdSlotInterface) {
            return;
        }

        if (true === $args->hasChangedField('visible') && true === $args->getNewValue('visible')) {
            $adSlots = $entity->getAdSlots();
            // Make sure that there is only one slot refer to this LibraryInstance with visible = false.
            // Otherwise this script will be broken

            if ($adSlots instanceof PersistentCollection && $adSlots->count() > 0) {
                /** @var DisplayAdSlotInterface $adSlot */
                $adSlot = $adSlots->current();
                $adTags = $adSlot->getAdTags();
                foreach ($adTags as $adTag) {
                    /**
                     * @var AdTagInterface $adTag
                     */
                    $libraryAdTag = $adTag->getLibraryAdTag();
                    if (!$libraryAdTag->getVisible()) {
                        $libraryAdTag->setVisible(true);
                    }

                    $librarySlotTag = new LibrarySlotTag();
                    $librarySlotTag->setActive($adTag->isActive());
                    $librarySlotTag->setRotation($adTag->getRotation());
                    $librarySlotTag->setPosition($adTag->getPosition());
                    $librarySlotTag->setFrequencyCap($adTag->getFrequencyCap());
                    $librarySlotTag->setLibraryAdSlot($entity);
                    $librarySlotTag->setLibraryAdTag($adTag->getLibraryAdTag());
                    $librarySlotTag->setRefId($adTag->getRefId());

                    $entity->getLibSlotTags()->add($librarySlotTag); // add to LibrarySlot

                    $this->newSlotTags[] = $librarySlotTag;
                }
            }
        }
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        if (!empty($this->newSlotTags)) {
            $em = $event->getEntityManager();
            foreach ($this->newSlotTags as $slotTag) {
                $em->persist($slotTag);
            }

            $this->newSlotTags = []; // reset new slot tag array

            $em->flush();
        }
    }
}