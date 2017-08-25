<?php

namespace Tagcade\Bundle\AppBundle\EventListener;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Cache\Video\Refresher\VideoWaterfallTagCacheRefresherInterface;
use Tagcade\Entity\Core\IvtPixelWaterfallTag;
use Tagcade\Model\Core\IvtPixelInterface;
use Tagcade\Model\Core\IvtPixelWaterfallTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Repository\Core\IvtPixelWaterfallTagRepositoryInterface;

class IvtPixelChangeListener
{
    /**
     * @var VideoWaterfallTagCacheRefresherInterface
     */
    private $cacheRefresher;

    /** @var IvtPixelWaterfallTagRepositoryInterface */
    private $ivtPixelWaterfallTagRepository;

    /** @var array */
    private $changeVideoWaterfallTags;

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

        if (!$entity instanceof IvtPixelInterface) {
            return;
        }
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->ivtPixelWaterfallTagRepository = $args->getEntityManager()->getRepository(IvtPixelWaterfallTag::class);

        if (!$entity instanceof IvtPixelInterface) {
            return;
        }

        $changeSets = $args->getEntityChangeSet();

        if (count($changeSets) < 2 && array_key_exists(IvtPixelInterface::NAME, $changeSets)) {
            return;
        }

        $this->calculateVideoWaterfallTagChangeByIvtPixel($entity);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->ivtPixelWaterfallTagRepository = $args->getEntityManager()->getRepository(IvtPixelWaterfallTag::class);

        if (!$entity instanceof IvtPixelInterface) {
            return;
        }

//        $this->calculateVideoWaterfallTagChangeByIvtPixel($entity);
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

    /**
     * @param IvtPixelInterface $ivtPixel
     */
    private function calculateVideoWaterfallTagChangeByIvtPixel(IvtPixelInterface $ivtPixel)
    {
        $ivtVideoWaterfallTags = $this->ivtPixelWaterfallTagRepository->getIvtPixelWaterfallTagsByIvtPixel($ivtPixel);

        if ($ivtVideoWaterfallTags instanceof Collection) {
            $ivtVideoWaterfallTags = $ivtVideoWaterfallTags->toArray();
        }

        foreach ($ivtVideoWaterfallTags as $ivtVideoWaterfallTag) {
            if (!$ivtVideoWaterfallTag instanceof IvtPixelWaterfallTagInterface) {
                continue;
            }

            $videoWaterfallTag = $ivtVideoWaterfallTag->getWaterfallTag();

            if (!$videoWaterfallTag instanceof VideoWaterfallTagInterface) {
                continue;
            }

            $this->changeVideoWaterfallTags[] = $videoWaterfallTag;
        }
    }
}