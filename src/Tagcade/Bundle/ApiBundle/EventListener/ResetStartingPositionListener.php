<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Entity\Core\LibraryAdSlotAbstract;
use Tagcade\Entity\Core\LibraryExpression;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\PositionInterface;
use Tagcade\Repository\Core\LibraryAdSlotRepositoryInterface;
use Tagcade\Repository\Core\LibraryExpressionRepositoryInterface;

class ResetStartingPositionListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function postSoftDelete(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if(!$entity instanceof PositionInterface || ($entity instanceof AdTagInterface && $entity->isInLibrary())) {
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

        if ($entity instanceof LibraryExpressionInterface && $args->hasChangedField('expectLibraryAdSlot')) {
            /** @var LibraryAdSlotRepositoryInterface $libraryAdSlotRepository */
            $libraryAdSlotRepository = $args->getEntityManager()->getRepository(LibraryAdSlotAbstract::class);
            $newLibraryAdSLot = $libraryAdSlotRepository->find($args->getNewValue('expectLibraryAdSlot'));

            if(!$newLibraryAdSLot instanceof LibraryDisplayAdSlotInterface && !$newLibraryAdSLot instanceof LibraryNativeAdSlotInterface) {
                throw new LogicException('expect a LibraryDisplayAdSlotInterface or a LibraryNativeAdSlotInterface  object');
            }

            if ($newLibraryAdSLot instanceof LibraryDisplayAdSlotInterface) {
                $this->resetStartingPositionDueToExpectLibraryAdSlotChange($entity, $newLibraryAdSLot, $args);
            }

            return;
        }

        if(!$entity instanceof PositionInterface ||
            ($entity instanceof PositionInterface && (!$args->hasChangedField('position')) && !$args->hasChangedField('active')) ||
            ($entity instanceof AdTagInterface && $entity->isInLibrary())) {
            return;
        }

        $this->resetStartingPositionForExpression($entity, $args);
    }

    /**
     * @param LibraryExpressionInterface $libraryExpression
     * @param LibraryDisplayAdSlotInterface $newLibraryDisplayAdSlot
     * @param PreUpdateEventArgs $args
     */
    protected function resetStartingPositionDueToExpectLibraryAdSlotChange(LibraryExpressionInterface $libraryExpression, LibraryDisplayAdSlotInterface $newLibraryDisplayAdSlot, PreUpdateEventArgs $args)
    {
        $librarySlotTags  = $newLibraryDisplayAdSlot->getLibSlotTags()->toArray();
        $maxPosition = $this->getMaxPosition($librarySlotTags);

        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();
        $md = $em->getClassMetadata(LibraryExpression::class);
        $startingPosition = $libraryExpression->getStartingPosition();
        if (null !== $startingPosition && ($startingPosition > $maxPosition)) {
            $libraryExpression->setStartingPosition(null);
            $uow->recomputeSingleEntityChangeSet($md, $libraryExpression);
//            $em->merge($libraryExpression);
        }
    }

    /**
     * Get max position of ad tags within the same ad slot
     * @param array $adTags
     * @return int|mixed
     */
    protected function getMaxPosition(array $adTags)
    {
        $adTags = array_filter($adTags, function (PositionInterface $t) {
                return null === $t->getDeletedAt() && $t->isActive();
            }); // get active tag

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

        return $max;
    }

    protected function resetStartingPositionForExpression(PositionInterface $adTag, LifecycleEventArgs $args)
    {
        $displayAdSlot = $adTag->getContainer();
        if(!$displayAdSlot instanceof DisplayAdSlotInterface && !$displayAdSlot instanceof LibraryDisplayAdSlotInterface) {
            return;
        }

        $adTags = $adTag->getSiblings();
        $max = $this->getMaxPosition($adTags->toArray());

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