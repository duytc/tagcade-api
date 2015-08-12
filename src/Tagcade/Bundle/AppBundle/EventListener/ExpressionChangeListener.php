<?php

namespace Tagcade\Bundle\AppBundle\EventListener;


use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Model\Core\ExpressionInterface;

class ExpressionChangeListener
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    protected $changedEntities;

    function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
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
        if (!isset($this->changedEntities) || !is_array($this->changedEntities) || count($this->changedEntities) < 1) {
            return;
        }

        $changedEntities = $this->changedEntities;

        unset($this->changedEntities);

        $adSlots = [];

        // filter all adTags and not (in $adSlots and in $adNetworks)
        array_walk($changedEntities,
            function($entity) use (&$adSlots)
            {
                if (!$entity instanceof ExpressionInterface)
                {
                    return false;
                }

                $affectingDynamicAdSlot = $entity->getDynamicAdSlot();

                if (null === $entity->getDeletedAt() && !$affectingDynamicAdSlot->getExpressions()->contains($entity)) { // include the entity being inserted
                    $affectingDynamicAdSlot->getExpressions()->add($entity);
                }
                else if (null !== $entity->getDeletedAt()) { // remove expression
                    $removeElement = array_filter($affectingDynamicAdSlot->getExpressions()->toArray(), function(ExpressionInterface $e) use($entity) { return $e->getId() === $entity->getId();});
                    $removeElement = current($removeElement);
                    if ($removeElement instanceof ExpressionInterface) {
                        $affectingDynamicAdSlot->getExpressions()->removeElement($removeElement);
                    }
                }

                if (!in_array($affectingDynamicAdSlot, $adSlots)) {
                    $adSlots[] = $affectingDynamicAdSlot;
                }

                return true;
            }
        );

        if (count($adSlots) > 0) {
            $this->eventDispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($adSlots));
        }

    }
} 