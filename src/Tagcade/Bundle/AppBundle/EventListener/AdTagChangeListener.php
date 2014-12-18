<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagcade\Bundle\AppBundle\Event\UpdateCacheEvent;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;

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

    function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $changedEntities = array_merge($uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates(), $uow->getScheduledEntityDeletions());

        $this->changedEntities = $changedEntities;
    }

    // Truly refresh cache invocation
    public function postFlush(PostFlushEventArgs $args)
    {
        if (null === $this->changedEntities || count($this->changedEntities) < 1) {
            return;
        }

        $changedEntities = $this->changedEntities;

        unset($this->changedEntities);

        // filter all adNetworks refresh cache for adNetworks
        $adNetworks = array_filter($changedEntities,
            function($entity)
            {
                if (!$entity instanceof AdNetworkInterface)
                {
                    return false;
                }

                return true;
            }
        );


        // filter all adSlots and not in $adNetworks
        $adSlots = array_filter($changedEntities,
            function($entity) use ($adNetworks)
            {
                if (!$entity instanceof AdSlotInterface)
                {
                    return false;
                }

                /**
                 * @var AdNetworkInterface $adNetwork
                 */
                foreach ($adNetworks as $adNetwork) {
                    $adTags = $adNetwork->getAdTags();
                    /**
                     * @var AdTagInterface $adTag
                     */
                    foreach ($adTags as $adTag) {
                        if ($entity->getId() === $adTag->getAdSlot()->getId()) {
                            return false;
                        }
                    }
                }

                return true;
            }
        );

        // filter all adTags and not (in $adSlots and in $adNetworks)
        array_filter($changedEntities,
            function($entity) use ($adNetworks, &$adSlots)
            {
                if (!$entity instanceof AdTagInterface)
                {
                    return false;
                }

                /**
                 * @var AdNetworkInterface $adNetwork
                 */
                foreach ($adNetworks as $adNetwork) {
                    if(in_array($entity, $adNetwork->getAdTags())) {
                        return false;
                    }
                }

                // ignore the ad tag in adSlot has been counted
                if (in_array($entity->getAdSlot(), $adSlots)) {
                    return false;
                }

                $adSlots[] = $entity->getAdSlot();

                return true;
            }
        );


        $updateList = array_merge($adNetworks, $adSlots);

        if (count($updateList) > 0) {
            $this->eventDispatcher->dispatch(UpdateCacheEvent::NAME, new UpdateCacheEvent($updateList));
        }

    }

} 