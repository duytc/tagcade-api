<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Entity\Core\LibraryExpression;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Repository\Core\LibraryExpressionRepositoryInterface;

class ResetStartingPositionListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function postSoftDelete(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if(!$entity instanceof AdTagInterface) {
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
        if(!$entity instanceof AdTagInterface || ($entity instanceof AdTagInterface && !$args->hasChangedField('position'))) {
            return;
        }

        $this->resetStartingPositionForExpression($entity, $args);
    }

    protected function resetStartingPositionForExpression(AdTagInterface $adTag, LifecycleEventArgs $args)
    {
        $displayAdSlot = $adTag->getAdSlot();
        if(!$displayAdSlot instanceof DisplayAdSlotInterface) {
            return;
        }

        $adTags = $displayAdSlot->getAdTags();
        $adTags = array_filter($adTags->toArray(), function(AdTagInterface $t) { return null === $t->getDeletedAt() && $t->isActive(); }); // get active tag

        $positions = array_map(function(AdTagInterface $tag) {
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
        $libraryExpressions = $repository->getByLibraryAdSlotAndStartingPosition($displayAdSlot->getLibraryAdSlot(), $max);
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