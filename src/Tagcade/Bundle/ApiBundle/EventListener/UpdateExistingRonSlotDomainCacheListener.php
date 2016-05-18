<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\PersistentCollection;
use Tagcade\Cache\ConfigurationCacheInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;

/**
 * This listener will update redis hash that store mapping (ron slot, domain)=>ad slot id to tell existence of image of ron slot in a domain (which is the ad slot)
 *
 * Class UpdateExistingRonSlotDomainCacheListener
 * @package Tagcade\Bundle\ApiBundle\EventListener
 */
class UpdateExistingRonSlotDomainCacheListener
{
    /**
     * @var ConfigurationCacheInterface
     */
    private $configCache;

    function __construct(ConfigurationCacheInterface $configCache)
    {
        $this->configCache = $configCache;
    }

    /**
     * IF new ad slot is created, it could be from ron ad slot hence we need to add to its existence list
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof BaseAdSlotInterface) {
            return;
        }

        $this->configCache->addAdSlotToRonSlotDomainCache($entity);
    }

    /**
     * Process when ad slot is removed or the ron ad slot is removed
     *
     * @param LifecycleEventArgs $args
     */
    public function postSoftDelete(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof BaseAdSlotInterface) {
            $this->configCache->removeRonSlotDomainCacheForAdSlot($entity);
            return;
        }

        if ($entity instanceof RonAdSlotInterface) {
            /** @var Collection|BaseAdSlotInterface[] $adSlots */
            $adSlots = $entity->getLibraryAdSlot()->getAdSlots();

            if ($adSlots instanceof PersistentCollection) {
                $adSlots = $adSlots->toArray();
            }

            $this->configCache->removeRonSlotDomainCacheForAdSlots($adSlots);
        }
    }
}