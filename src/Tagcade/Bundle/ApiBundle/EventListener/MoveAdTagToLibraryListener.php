<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;

/**
 * This listener will be triggered when an ad slot is moved to library. The listener will then update all tags in side that slot to library
 * Class MoveAdTagToLibraryListener
 * @package Tagcade\Bundle\ApiBundle\EventListener
 */
class MoveAdTagToLibraryListener {

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if(!$entity instanceof LibraryDisplayAdSlotInterface && !$entity instanceof LibraryNativeAdSlotInterface) {
            return;
        }

        if (true === $args->hasChangedField('visible') && true === $args->getNewValue('visible')) {
            $adSlots = $entity->getAdSlots();
            foreach ($adSlots as $adSlot) {
                /**
                 * @var BaseAdSlotInterface $adSlot
                 */
                $adTags = $adSlot->getAdTags();
                foreach($adTags as $adTag) {
                    /**
                     * @var AdTagInterface $adTag
                     */
                    $libraryAdTag = $adTag->getLibraryAdTag();
                    if (!$libraryAdTag->getVisible()) {
                        $libraryAdTag->setVisible(true);
                    }
                }
            }
        }
    }

} 