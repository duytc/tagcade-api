<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\Core\RonAdSlotInterface;

class AdTagChangeListener
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    protected $changedEntities;

    protected $preSoftDeleteAdTags = [];

    function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function preSoftDelete(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $this->preSoftDeleteAdTags = array_merge(
            $this->preSoftDeleteAdTags,
            array_filter(
                $uow->getScheduledEntityDeletions(),
                function($entity)
                {
                    return $entity instanceof AdTagInterface || $entity instanceof LibrarySlotTagInterface;
                }
            )
        );
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $tmp = array_merge($uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates(), $uow->getScheduledEntityDeletions());

        $this->changedEntities = $tmp;
    }

    // Truly refresh cache invocation
    public function postFlush(PostFlushEventArgs $args)
    {
        $changedEntities = array_merge($this->preSoftDeleteAdTags, $this->changedEntities);

        $this->changedEntities = [];
        $this->preSoftDeleteAdTags = [];

        $adSlots = [];

        // filter all adTags and not (in $adSlots and in $adNetworks)
        array_walk($changedEntities,
            function($entity) use (&$adSlots)
            {
                if (!$entity instanceof AdTagInterface && !$entity instanceof LibraryAdTagInterface && !$entity instanceof LibrarySlotTagInterface)
                {
                    return false;
                }

                $adTags = [];

                if ($entity instanceof LibraryAdTagInterface ) {
                    $adTags = array_merge($adTags, $entity->getAdTags()->toArray());
                }
                else if($entity instanceof LibrarySlotTagInterface) {
                    // also update cache of RonAdTag which is known as LibrarySlotTag
                    $ronAdSlot = $entity->getLibraryAdSlot()->getRonAdSlot();
                    if ($ronAdSlot instanceof RonAdSlotInterface) {
                        $adSlots[] = $ronAdSlot;
                    }
                }
//                else if ($entity instanceof LibrarySlotTagInterface) {
//                    $tmpAdTags = $entity->getLibraryAdTag()->getAdTags()->toArray();
//                    $tmpAdTags = array_filter( // filter for ad tags in the same library slot
//                        $tmpAdTags,
//                        function(WaterfallTagInterface $adTag) use($entity) {
//                            return $adTag->getAdSlot()->getLibraryAdSlot()->getId() === $entity->getLibraryAdSlot()->getId();
//                        }
//                    );
//
//                    $adTags = array_merge($adTags, $tmpAdTags);
//                }
                else {
                    $adTags = array_merge($adTags, [$entity]);
                }

                if (is_null($adTags)) { // ignore when update library with no tag reference
                    return false;
                }

                foreach($adTags as $tag) {
                    /**
                     * @var AdTagInterface $tag
                     */
                    // ignore the ad tag that is not belonged to any AdSlot
                    if(null === $tag->getAdSlot()) continue;

                    $updatingAdSlot = $tag->getAdSlot();
                    // ignore the ad tag in adSlot has been counted
                    if (in_array($updatingAdSlot, $adSlots)) {
                        continue;
                    }

                    if (null === $tag->getDeletedAt() && !$updatingAdSlot->getAdTags()->contains($tag)) { // include the entity being inserted
                        $updatingAdSlot->getAdTags()->add($tag);
                    }
                    else if(null !== $tag->getDeletedAt()) {
                        $removeElement = array_filter($updatingAdSlot->getAdTags()->toArray(), function(AdTagInterface $t) use($tag) { return $t->getId() === $tag->getId();});
                        $removeElement = current($removeElement);
                        if ($removeElement instanceof AdTagInterface) {
                            $updatingAdSlot->getAdTags()->removeElement($removeElement);
                        }
                    }

                    $adSlots[] = $updatingAdSlot;
                }


                return true;
            }
        );

        if (count($adSlots) > 0) {
            $this->eventDispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($adSlots));
        }

    }

} 