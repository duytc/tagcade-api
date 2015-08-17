<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\PersistentCollection;
use Tagcade\Entity\Core\LibrarySlotTag;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;

/**
 * This listener will be triggered when an ad slot is moved to library. The listener will then update all tags in side that slot to library
 * Class MoveAdTagToLibraryListener
 * @package Tagcade\Bundle\ApiBundle\EventListener
 */
class MoveDynamicAdSlotToLibraryListener
{
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if(!$entity instanceof LibraryDynamicAdSlotInterface) {
            return;
        }

        if (true === $args->hasChangedField('visible') && true === $args->getNewValue('visible')) {

            $em = $args->getEntityManager();
            $adSlots = $entity->getAdSlots();

            if($adSlots instanceof PersistentCollection) {
                /** @var DynamicAdSlotInterface $adSlot */
                $adSlot = $adSlots->current();

                if ($adSlot instanceof DynamicAdSlotInterface) {
                    $referencedAdSlots = $this->getReferencedAdSlotsForDynamicAdSlot($adSlot);
                    foreach ($referencedAdSlots as $slot) {
                        /**
                         * @var BaseAdSlotInterface $slot
                         */
                        $lib = $slot->getLibraryAdSlot();
                        if (!$lib->isVisible()) {
                            $lib->setVisible(true);
                            $em->persist($lib);
                        }
                    }
                }
            }
        }
    }

    protected function getReferencedAdSlotsForDynamicAdSlot(DynamicAdSlotInterface $dynamicAdSlot)
    {
        $results = [];

        $defaultAdSlot = $dynamicAdSlot->getDefaultAdSlot();
        if($defaultAdSlot instanceof DisplayAdSlotInterface || $defaultAdSlot instanceof NativeAdSlotInterface) {
            $results[] = $defaultAdSlot;
        }

        $expressions = $dynamicAdSlot->getExpressions();
        /** @var ExpressionInterface $expression */
        foreach($expressions as $expression) {
            $expectAdSlot = $expression->getExpectAdSlot();
            if($expectAdSlot instanceof DisplayAdSlotInterface || $expectAdSlot instanceof NativeAdSlotInterface) {
                $results[] = $expectAdSlot;
            }
        }

        return array_unique($results);
    }
} 