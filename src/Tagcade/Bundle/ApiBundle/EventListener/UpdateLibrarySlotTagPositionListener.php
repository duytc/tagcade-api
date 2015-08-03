<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\PersistentCollection;
use Tagcade\Entity\Core\LibrarySlotTag;
use Tagcade\Model\Core\LibrarySlotTagInterface;

class UpdateLibrarySlotTagPositionListener {

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $entities = array_merge($uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates(), $uow->getScheduledEntityDeletions());
        $md = $em->getClassMetadata(LibrarySlotTag::class);

        foreach ($entities as $entity) {
            if (!$entity instanceof LibrarySlotTagInterface) {
                continue;
            }

            $affectedAdTags = $this->updateLibrarySlotTagPosition($entity);

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
        if(!$entity instanceof LibrarySlotTagInterface) {
            return;
        }

        $this->updateLibrarySlotTagPosition($entity);
    }

    /**
     * Update librarySlotTag position. It should be continues according to current list of ad tag in the same ad slot
     *
     * @param LibrarySlotTagInterface $updatingLibrarySlotTag
     * @return array
     */
    protected function updateLibrarySlotTagPosition(LibrarySlotTagInterface $updatingLibrarySlotTag)
    {
        $libraryAdSlot = $updatingLibrarySlotTag->getLibraryAdSlot();

        $librarySlotTags = $libraryAdSlot->getLibSlotTags();

        if($librarySlotTags instanceof PersistentCollection) $librarySlotTags = $librarySlotTags->toArray();

        $this->correctLibrarySlotTagPositionInList($updatingLibrarySlotTag, $librarySlotTags);

        return $this->updatePositionForLibrarySlotTags($librarySlotTags);
    }


    protected function correctLibrarySlotTagPositionInList(LibrarySlotTagInterface &$updatingLibrarySlotTag, array $librarySlotTags)
    {
        // sort array asc with respect to position
        usort($librarySlotTags, function(LibrarySlotTagInterface $a, LibrarySlotTagInterface $b) {
            if ($a->getPosition() == $b->getPosition()) {
                return 0;
            }
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });

        // list out all positions
        $positions = array();
        array_walk(
            $librarySlotTags,
            function(LibrarySlotTagInterface $librarySlotTag) use(&$positions) {
                if (!in_array($librarySlotTag->getPosition(), $positions)) {
                    array_push($positions, count($positions) + 1);
                }
            }
        );

        $max = empty($positions) ? 1 : max($positions) + 1;;

        $targetPosition = $updatingLibrarySlotTag->getPosition();
        // Current updating ad tag will have position $max + 1 if the position is out of bound [1, max]
        // or 1 if position is null
        if ($targetPosition == null || $targetPosition > $max) {
            $updatingLibrarySlotTag->setPosition($max);
        }
    }

    /**
     * Return sorted library slot tag based on position
     * @param array $librarySlotTags
     * @return array
     */
    protected function updatePositionForLibrarySlotTags(array $librarySlotTags)
    {
        // if target position in range of min max then we have to make sure the range is continuous
        $groups = array();
        foreach ($librarySlotTags as $librarySlotTag) {
            /**
             * @var LibrarySlotTagInterface $librarySlotTag
             */
            $groups[$librarySlotTag->getPosition()][] = $librarySlotTag;
        }

        // truly update position
        $pos = 1;
        $updatedLibrarySlotTags = [];
        while(array_key_exists($pos, $groups)) {
            $librarySlotTagList = $groups[$pos];
            foreach ($librarySlotTagList as $librarySlotTag) {
                if ($librarySlotTag->getPosition() != $pos) {
                    $librarySlotTag->setPosition($pos);
                    $updatedLibrarySlotTags[] = $librarySlotTag;
                }

                continue;
            }

            $pos ++;
        }

        return $updatedLibrarySlotTags;
    }
}