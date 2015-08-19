<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Entity\Core\LibraryExpression;
use Tagcade\Model\Core\AdTag;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\PositionInterface;
use Tagcade\Repository\Core\LibraryExpressionRepositoryInterface;

class ResetStartingPositionListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function postSoftDelete(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if(!$entity instanceof PositionInterface) {
            return;
        }

        $this->resetStartingPositionForExpression($entity, $args);
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if((!$entity instanceof PositionInterface) || ($entity instanceof PositionInterface && (!$args->hasChangedField('position') && !$args->hasChangedField('active')))) {
            return;
        }

        $this->resetStartingPositionForExpression($entity, $args);
    }

    /**
     * @param PositionInterface $adTag the tag to be deleted or updated position
     * @param LifecycleEventArgs $args
     */
    protected function resetStartingPositionForExpression(PositionInterface $adTag, LifecycleEventArgs $args)
    {
        $displayAdSlot = $adTag->getContainer();
        if(!$displayAdSlot instanceof DisplayAdSlotInterface && !$displayAdSlot instanceof LibraryDisplayAdSlotInterface) {
            return;
        }

//        $adTags = $displayAdSlot->getAdTags();
        $adTags = $adTag->getSiblings();
        $adTags = array_filter($adTags->toArray(), function(PositionInterface $t) { return null === $t->getDeletedAt() && $t->isActive(); }); // get active tag

        $positions = array_map(function(PositionInterface $tag) {
            return $tag->getPosition();
        }, $adTags);

        if (null === $positions || empty($positions)) {
            $max = 0;
        }
        else {
            $count = count($positions);
            $max = max($positions);
            if ($max > $count) {
                $max = $count;
            }
        }

        /**
         * @var LibraryExpressionRepositoryInterface $repository
         */
        $em = $args->getEntityManager();
        $repository = $em->getRepository(LibraryExpression::class);
        $libraryAdSlot = $adTag instanceof AdTagInterface ? $displayAdSlot->getLibraryAdSlot() : $displayAdSlot;
        $libraryExpressions = $repository->getByLibraryAdSlotAndStartingPosition($libraryAdSlot, $max);
        /**
         * @var
         */
        foreach($libraryExpressions as $libraryExpression) {
            if ($libraryExpression->getStartingPosition() !== null) {
                $libraryExpression->setStartingPosition(null); // reset position
                $em->merge($libraryExpression);
            }
        }
    }
}