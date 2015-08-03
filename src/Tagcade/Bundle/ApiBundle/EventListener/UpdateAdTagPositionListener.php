<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\PersistentCollection;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Entity\Core\AdTag;

/**
 *
 * This listener only supports updating position for ad tags in single ad slot.
 * To make it works with library ad slots you have to set position manually for ad tags in library ad slot and flush.
 * Then this listener will continue its job which is making sure position of ad tags in each slot is properly set
 *
 * Class UpdateAdTagPositionListener
 * @package Tagcade\Bundle\ApiBundle\EventListener
 */
class UpdateAdTagPositionListener
{
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $entities = array_merge($uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates(), $uow->getScheduledEntityDeletions());
        $md = $em->getClassMetadata(AdTag::class);

        foreach ($entities as $entity) {
            if (!$entity instanceof AdTagInterface) {
                continue;
            }

            $affectedAdTags = $this->updateTagPosition($entity);

            foreach ($affectedAdTags as $adTag) {
                $uow->recomputeSingleEntityChangeSet($md, $adTag);
            }
        }
    }
    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if(!$entity instanceof AdTagInterface) {
            return;
        }

        $this->updateTagPosition($entity);
    }

    /**
     * Update AdTag position. It should be continues according to current list of ad tag in the same ad slot
     *
     * @param AdTagInterface $updatingAdTag
     * @return array
     */
    protected function updateTagPosition(AdTagInterface $updatingAdTag)
    {
        $adSlot = $updatingAdTag->getAdSlot();

        if($adSlot instanceof ReportableAdSlotInterface)
        {
            return $this->updatePositionForAdSlot($updatingAdTag);
        }
    }

    protected function updatePositionForAdSlot(AdTagInterface $updatingAdTag)
    {
        $adSlot = $updatingAdTag->getAdSlot();

        if (!$adSlot instanceof ReportableAdSlotInterface) {
            throw new InvalidArgumentException('expect instance of ReportableAdSlotInterface');
        }

        $adTags = $adSlot->getAdTags()->toArray();

        $this->correctAdTagPositionInList($updatingAdTag, $adTags);

        return $this->updatePositionForAdTags($adTags);
    }

    protected function correctAdTagPositionInList(AdTagInterface &$updatingAdTag, array $adTags)
    {
        // sort array asc with respect to position
        usort($adTags, function(AdTagInterface $a, AdTagInterface $b) {
            if ($a->getPosition() == $b->getPosition()) {
                return 0;
            }
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });

        // list out all positions
        $positions = array();
        array_walk(
            $adTags,
            function(AdTagInterface $adTag) use(&$positions) {
                if (!in_array($adTag->getPosition(), $positions)) {
                    array_push($positions, count($positions) + 1);
                }
            }
        );

        $max = empty($positions) ? 1 : max($positions) + 1;;

        $targetPosition = $updatingAdTag->getPosition();
        // Current updating ad tag will have position $max + 1 if the position is out of bound [1, max]
        // or 1 if position is null
        if ($targetPosition == null || $targetPosition > $max) {
            $updatingAdTag->setPosition($max);
        }
    }


    /**
     * Return sorted tags based on position
     * @param array $adTags
     * @return array
     */
    protected function updatePositionForAdTags(array $adTags)
    {
        // if target position in range of min max then we have to make sure the range is continuous
        $groups = array();
        foreach ($adTags as $adTag) {
            /**
             * @var AdTagInterface $adTag
             */
            $groups[$adTag->getPosition()][] = $adTag;
        }

        // truly update position
        $pos = 1;
        $updatedAdTags = [];
        while(array_key_exists($pos, $groups)) {
            $adTagList = $groups[$pos];
            foreach ($adTagList as $adTag) {
                if ($adTag->getPosition() != $pos) {
                    $adTag->setPosition($pos);
                    $updatedAdTags[] = $adTag;
                }

                continue;
            }

            $pos ++;
        }

        return $updatedAdTags;
    }
} 