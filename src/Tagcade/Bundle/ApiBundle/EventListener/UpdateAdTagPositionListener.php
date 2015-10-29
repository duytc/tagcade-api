<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\PersistentCollection;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\PositionInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;

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
    private $presoftDeleteAdTags = [];

    public function preSoftDelete(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $this->presoftDeleteAdTags = array_merge($this->presoftDeleteAdTags, array_filter($uow->getScheduledEntityDeletions(), function($entity) {
            return ($entity instanceof LibrarySlotTagInterface || ($entity instanceof AdTagInterface && !$entity->isInLibrary()));
          }
        ));
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $preSoftDeleteAdTags = $this->presoftDeleteAdTags;
        $this->presoftDeleteAdTags = [];

        $entities = array_merge($uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates(), $uow->getScheduledEntityDeletions(), $preSoftDeleteAdTags);

        foreach ($entities as $entity) {
            if (!$entity instanceof PositionInterface) {
                continue;
            }

            $md = $em->getClassMetadata($entity->getClassName());
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
        if(!$entity instanceof PositionInterface) {
            return;
        }

        $this->updateTagPosition($entity);
    }

    /**
     * Update AdTag position. It should be continues according to current list of ad tag in the same ad slot
     *
     * @param PositionInterface $updatingAdTag
     * @return array
     */
    protected function updateTagPosition(PositionInterface $updatingAdTag)
    {
        $adSlot = $updatingAdTag->getContainer();

        if($adSlot instanceof ReportableAdSlotInterface || $adSlot instanceof BaseLibraryAdSlotInterface)
        {
            return $this->updatePositionForAdSlot($updatingAdTag);
        }

        throw new LogicException('not support tag of something other than ReportableAdSlotInterface or BaseLibraryAdSlotInterface');
    }

    protected function updatePositionForAdSlot(PositionInterface $updatingAdTag)
    {
        $listTags = $updatingAdTag->getSiblings();

        if($listTags instanceof ArrayCollection || $listTags instanceof PersistentCollection) $listTags = $listTags->toArray();

        if(null === $listTags) $listTags = [];

        $adTags = array_filter($listTags, function(PositionInterface $t) { return null === $t->getDeletedAt();});

        $updatedAdTags = $this->correctAdTagPositionInList($updatingAdTag, $adTags);

        return array_merge($updatedAdTags, $this->updatePositionForAdTags($adTags));
    }

    /**
     * This method will make sure the position of $updatingAdTag is in range with other positions as in $adTags
     *
     * @param mixed $updatingAdTag
     * @param array $adTags
     * @return array
     */
    protected function correctAdTagPositionInList(&$updatingAdTag, array &$adTags)
    {
        if(!$updatingAdTag instanceof PositionInterface) {
            return [];
        }
        // sort array asc with respect to position
        usort($adTags, function(PositionInterface $a, PositionInterface $b) {
            if($a->getPosition() === null) return 1;
            if($b->getPosition() === null) return -1;
            if ($a->getPosition() == $b->getPosition()) {
                return 0;
            }
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });

        // list out all positions
        $updatedAdTags = [];
        $positions = array();
        $mappedPositions = array();
        array_walk(
            $adTags,
            function(PositionInterface $adTag) use(&$positions, &$updatedAdTags, &$mappedPositions) {
                $myPos = $adTag->getPosition();
                $newPos = !array_key_exists($myPos, $mappedPositions) ? count($positions) + 1 : $mappedPositions[$myPos];
                $mappedPositions[$myPos] = $newPos;
                if (!in_array($newPos, $positions)) {
                    array_push($positions, $newPos);
                }
                if ($newPos !== $myPos) {
                    $adTag->setPosition($mappedPositions[$myPos]);
                    $updatedAdTags[] = $adTag;
                }
            }
        );

        $max = empty($positions) ? 1 : max($positions) + 1;

        if (in_array($updatingAdTag, $adTags)) {
            $max = $max - 1;
        }

        $targetPosition = $updatingAdTag->getPosition();
        // Current updating ad tag will have position $max + 1 if the position is out of bound [1, max]
        // or 1 if position is null
        if ($targetPosition == null || $targetPosition > $max) {
            $updatingAdTag->setPosition($max);
        }

        return $updatedAdTags;
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
             * @var PositionInterface $adTag
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