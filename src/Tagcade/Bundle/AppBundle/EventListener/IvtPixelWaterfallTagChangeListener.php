<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Cache\Video\Refresher\VideoWaterfallTagCacheRefresherInterface;
use Tagcade\Model\Core\IvtPixelWaterfallTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;

class IvtPixelWaterfallTagChangeListener
{
    /**
     * @var VideoWaterfallTagCacheRefresherInterface
     */
    private $cacheRefresher;

    /** @var array */
    private $changeVideoWaterfallTags = [];

    function __construct(VideoWaterfallTagCacheRefresherInterface $cacheRefresher)
    {
        $this->cacheRefresher = $cacheRefresher;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof IvtPixelWaterfallTagInterface) {
            return;
        }

        $videoWaterfallTag = $entity->getWaterfallTag();

        if (!$videoWaterfallTag instanceof VideoWaterfallTagInterface) {
            return;
        }

        $this->cacheRefresher->refreshVideoWaterfallTag($videoWaterfallTag);
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof IvtPixelWaterfallTagInterface) {
            return;
        }

        /** @var VideoWaterfallTagInterface $oldWaterfallTag */
        $oldWaterfallTag = $args->getOldValue('waterfallTag');

        /** @var VideoWaterfallTagInterface $newWaterfallTag */
        $newWaterfallTag = $args->getNewValue('waterfallTag');

        if ($oldWaterfallTag->getId() == $newWaterfallTag->getId()) {
            return;
        }

        $this->changeVideoWaterfallTags[] = $oldWaterfallTag;
        $this->changeVideoWaterfallTags[] = $newWaterfallTag;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof IvtPixelWaterfallTagInterface) {
            return;
        }

        $this->changeVideoWaterfallTags[] = $entity->getWaterfallTag();
    }

    /**
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        if (count($this->changeVideoWaterfallTags) < 1) {
            return;
        }

        $copyChangeWaterfallTags = $this->changeVideoWaterfallTags;
        $this->changeVideoWaterfallTags = [];

        foreach ($copyChangeWaterfallTags as $videoWaterfallTag) {
            if (!$videoWaterfallTag instanceof VideoWaterfallTagInterface) {
                continue;
            }

            $this->cacheRefresher->refreshVideoWaterfallTag($videoWaterfallTag);
        }
    }
}