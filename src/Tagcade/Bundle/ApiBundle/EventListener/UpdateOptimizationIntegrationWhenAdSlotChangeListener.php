<?php


namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Worker\Manager;

class UpdateOptimizationIntegrationWhenAdSlotChangeListener
{
    const AD_SLOT = 'adSlot';
    const SITE = 'site';
    const OPTIMIZATION_INTEGRATION = 'optimizationIntegration';
    const ACTION = 'action';

    const ACTION_REMOVE = "Remove";
    const ACTION_ADD = "Add";

    /** @var Manager */
    private $manager;

    private $actions = [];

    /**
     * UpdateOptimizationIntegrationWhenAdSlotChangeListener constructor.
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof BaseAdSlotInterface) {
            return;
        }

        $this->assignOptimizationIntegrationToAdSlot($entity->getId(), $entity->getOptimizationIntegration(), $entity->getSite()->getId());
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof BaseAdSlotInterface) {
            return;
        }

        $oldOptimizationIntegration = $entity->getOptimizationIntegration();
        $newOptimizationIntegration = $entity->getOptimizationIntegration();

        if ($args->hasChangedField('optimizationIntegration')) {
            $oldOptimizationIntegration = $args->getOldValue('optimizationIntegration');
            $newOptimizationIntegration = $args->getNewValue('optimizationIntegration');
        }

        if ($oldOptimizationIntegration == $newOptimizationIntegration) {
            return;
        }

        if ($entity->isAutoOptimize()) {
            $this->removeOptimizationIntegrationFromAdSlot($entity->getId(), $oldOptimizationIntegration);
            $this->assignOptimizationIntegrationToAdSlot($entity->getId(), $newOptimizationIntegration, $entity->getSite()->getId());
        } else {
            $this->removeOptimizationIntegrationFromAdSlot($entity->getId(), $oldOptimizationIntegration);
            $this->removeOptimizationIntegrationFromAdSlot($entity->getId(), $newOptimizationIntegration);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof BaseAdSlotInterface) {
            return;
        }

        $this->removeOptimizationIntegrationFromAdSlot($entity->getId(), $entity->getOptimizationIntegration());
    }

    /**
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        if (empty($this->actions)) {
            return;
        }

        $actions = $this->actions;
        $this->actions = [];

        $this->manager->synchronizeAdSlotWithOptimizationIntegration($actions);
    }

    /**
     * @param $adSlotId
     * @param $optimizationIntegration
     * @param $siteId
     */
    private function assignOptimizationIntegrationToAdSlot($adSlotId, $optimizationIntegration, $siteId)
    {
        if (empty($optimizationIntegration)) {
            return;
        }

        $this->actions[] = [
            self::ACTION => self::ACTION_ADD,
            self::AD_SLOT => $adSlotId,
            self::SITE => $siteId,
            self::OPTIMIZATION_INTEGRATION => $optimizationIntegration
        ];
    }

    /**
     * @param $adSlotId
     * @param $optimizationIntegration
     */
    private function removeOptimizationIntegrationFromAdSlot($adSlotId, $optimizationIntegration)
    {
        if (empty($optimizationIntegration)) {
            return;
        }

        $this->actions[] = [
            self::ACTION => self::ACTION_REMOVE,
            self::AD_SLOT => $adSlotId,
            self::OPTIMIZATION_INTEGRATION => $optimizationIntegration
        ];
    }
}