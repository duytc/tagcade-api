<?php


namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Worker\Manager;

class UpdateOptimizationIntegrationWhenVideoWaterfallTagChangeListener
{
    const VIDEO_WATERFALL_TAG = 'videoWaterfallTag';
    const VIDEO_PUBLISHER = 'videoPublisher';
    const OPTIMIZATION_INTEGRATION = 'optimizationIntegration';
    const ACTION = 'action';

    const ACTION_REMOVE = 'Remove';
    const ACTION_ADD = 'Add';

    /** @var Manager */
    private $manager;

    private $actions = [];

    /**
     * UpdateOptimizationIntegrationWhenVideoWaterfallTagChangeListener constructor.
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
        $entity = $args->getEntity();
        if (!$entity instanceof VideoWaterfallTagInterface) {
            return;
        }

        $this->assignOptimizationIntegrationToVideoWaterfallTag($entity->getId(), $entity->getOptimizationIntegration(), $entity->getVideoPublisher()->getId());
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof VideoWaterfallTagInterface) {
            return;
        }


        if (!$args->hasChangedField('optimizationIntegration')) {
            return;
        }

        $oldOptimizationIntegration = $args->getOldValue('optimizationIntegration');
        $newOptimizationIntegration = $args->getNewValue('optimizationIntegration');

        if ($oldOptimizationIntegration == $newOptimizationIntegration) {
            return;
        }

        if ($entity->isAutoOptimize()) {
            $this->removeOptimizationIntegrationFromVideoWaterfallTag($entity->getId(), $oldOptimizationIntegration);
            $this->assignOptimizationIntegrationToVideoWaterfallTag($entity->getId(), $newOptimizationIntegration, $entity->getVideoPublisher()->getId());
        } else {
            $this->removeOptimizationIntegrationFromVideoWaterfallTag($entity->getId(), $oldOptimizationIntegration);
            $this->removeOptimizationIntegrationFromVideoWaterfallTag($entity->getId(), $newOptimizationIntegration);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof VideoWaterfallTagInterface) {
            return;
        }

        $this->removeOptimizationIntegrationFromVideoWaterfallTag($entity->getId(), $entity->getOptimizationIntegration());
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

        $this->manager->synchronizeVideoWaterfallTagWithOptimizationIntegration($actions);
    }

    /**
     * @param $videoWaterfallTagId
     * @param $optimizationIntegration
     * @param $videoPublisherId
     */
    private function assignOptimizationIntegrationToVideoWaterfallTag($videoWaterfallTagId, $optimizationIntegration, $videoPublisherId)
    {
        if (empty($optimizationIntegration)) {
            return;
        }

        $this->actions[] = [
            self::ACTION => self::ACTION_ADD,
            self::VIDEO_WATERFALL_TAG => $videoWaterfallTagId,
            self::VIDEO_PUBLISHER => $videoPublisherId,
            self::OPTIMIZATION_INTEGRATION => $optimizationIntegration
        ];
    }

    /**
     * @param $videoWaterfallTagId
     * @param $optimizationIntegration
     */
    private function removeOptimizationIntegrationFromVideoWaterfallTag($videoWaterfallTagId, $optimizationIntegration)
    {
        if (empty($optimizationIntegration)) {
            return;
        }

        $this->actions[] = [
            self::ACTION => self::ACTION_REMOVE,
            self::VIDEO_WATERFALL_TAG => $videoWaterfallTagId,
            self::OPTIMIZATION_INTEGRATION => $optimizationIntegration
        ];
    }
}