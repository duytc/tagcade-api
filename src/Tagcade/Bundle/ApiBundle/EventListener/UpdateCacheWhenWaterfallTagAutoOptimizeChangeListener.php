<?php


namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Worker\Manager;

class UpdateCacheWhenWaterfallTagAutoOptimizeChangeListener
{
    /** @var Manager */
    private $manager;

    private $changedWaterfallTags = [];

    /**
     * UpdateCacheWhenWaterfallTagAutoOptimizeChangeListener constructor.
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof VideoWaterfallTagInterface || !$args->hasChangedField('autoOptimize')) {
            return;
        }

        $this->changedWaterfallTags[] = $entity;
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if (count($this->changedWaterfallTags) < 1) {
            return;
        }

        $waterfallTags = $this->changedWaterfallTags;
        $this->changedWaterfallTags = [];

        $waterfallTagIds = [];
        foreach ($waterfallTags as $waterfallTag) {
            if (!$waterfallTag instanceof VideoWaterfallTagInterface) {
                continue;
            }

            $waterfallTagIds[] = $waterfallTag->getId();
        }

        $this->manager->updateCacheForVideoWaterfallTag($waterfallTagIds);
    }
}