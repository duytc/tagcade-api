<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Tagcade\Cache\ConfigurationCache;
use Tagcade\Cache\ConfigurationCacheInterface;
use Tagcade\Cache\Legacy\Cache\RedisArrayCacheInterface;
use Tagcade\Entity\Core\LibrarySlotTag;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\RonAdTagInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;

/**
 * This listener will update redis hash that store mapping (ron tag, slot)=>ad tag id to tell existence of image of ron tag in a slot (which is the ad tag)
 *
 * Class UpdateExistingRonTagSlotCacheListener
 * @package Tagcade\Bundle\ApiBundle\EventListener
 */
class UpdateExistingRonTagSlotCacheListener
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
     * IF new ad tag is created, it could be from ron ad tag hence we need to add to its existence list
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof AdTagInterface) {
            return;
        }

        $libraryAdSlot = $entity->getAdSlot()->getLibraryAdSlot();
        if (!$libraryAdSlot->getRonAdSlot() instanceof RonAdSlotInterface) {
            return; // the ad tag is not created from ron ad slot
        }

        /**
         * @var LibrarySlotTagRepositoryInterface $librarySlotTagRepository
         */
        $librarySlotTagRepository = $args->getEntityManager()->getRepository(LibrarySlotTag::class);
        $ronAdTag = $librarySlotTagRepository->getByLibraryAdSlotAndRefId($libraryAdSlot, $entity->getRefId());
        if (!$ronAdTag instanceof RonAdTagInterface) {
            throw new LogicException('there must be ron ad tag if the ad tag is created from ron ad slot');
        }

        $this->configCache->addAdTagToRonTagSlotCache($entity, $ronAdTag);
    }

    /**
     * Process when ad tag is removed or the ron ad tag is removed
     *
     * @param LifecycleEventArgs $args
     */
    public function postSoftDelete(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof AdTagInterface && $entity->getAdSlot()->getLibraryAdSlot()->isVisible()) {
            /**
             * @var LibrarySlotTagRepositoryInterface $librarySlotTagRepository
             */
            $librarySlotTagRepository = $args->getEntityManager()->getRepository(LibrarySlotTag::class);
            $libraryAdSlot = $entity->getAdSlot()->getLibraryAdSlot();
            $ronAdTag = $librarySlotTagRepository->getByLibraryAdSlotAndRefId($libraryAdSlot, $entity->getRefId());

            $this->configCache->removeRonTagSlotCacheForAdTag($entity, $ronAdTag);

            return;
        }

        if ($entity instanceof RonAdTagInterface) {
            $this->configCache->removeRonTagSlotCacheForRonAdTag($entity);
        }
    }


}