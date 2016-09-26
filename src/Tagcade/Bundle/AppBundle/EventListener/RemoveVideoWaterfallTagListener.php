<?php


namespace Tagcade\Bundle\AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Tagcade\Cache\Video\Refresher\VideoWaterfallTagCacheRefresherInterface;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;

class RemoveVideoWaterfallTagListener {

    /**
     * @var VideoWaterfallTagCacheRefresherInterface
     */
    private $cacheRefresher;

    /**
     * @var array
     */
    protected $changingVideoWaterfallTags;

    /**
     * @var array
     */
    protected $deletingVideoWaterfallTags;

    function __construct(VideoWaterfallTagCacheRefresherInterface $cacheRefresher)
    {
        $this->cacheRefresher = $cacheRefresher;
        $this->changingVideoWaterfallTags = [];
        $this->deletingVideoWaterfallTags = [];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preSoftDelete(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach($uow->getScheduledEntityDeletions() as $item) {
            if ($item instanceof VideoDemandAdTagInterface) {
                $id = $item->getVideoWaterfallTagItem()->getVideoWaterfallTag()->getId();
                if (!in_array($id, $this->deletingVideoWaterfallTags)) {
                    $this->deletingVideoWaterfallTags[] = $id;
                    $this->changingVideoWaterfallTags[] = $item->getVideoWaterfallTagItem()->getVideoWaterfallTag();
                }
            } else if ($item instanceof VideoWaterfallTagItemInterface) {
                $id = $item->getVideoWaterfallTag()->getId();
                if (!in_array($id, $this->deletingVideoWaterfallTags)) {
                    $this->deletingVideoWaterfallTags[] = $id;
                    $this->changingVideoWaterfallTags[] = $item->getVideoWaterfallTag();
                }
            } else if ($item instanceof VideoWaterfallTagInterface) {
                $this->cacheRefresher->removeVideoWaterfallTagCache($item);
            }
        }
    }


    public function postFlush(PostFlushEventArgs $args)
    {
        if (count($this->changingVideoWaterfallTags) < 1) {
            return;
        }

        foreach ($this->changingVideoWaterfallTags as $changingVideoWaterfallTag) {
            $this->cacheRefresher->refreshVideoWaterfallTag($changingVideoWaterfallTag);
        }

        $this->changingVideoWaterfallTags = [];
        $this->deletingVideoWaterfallTags = [];
    }
}