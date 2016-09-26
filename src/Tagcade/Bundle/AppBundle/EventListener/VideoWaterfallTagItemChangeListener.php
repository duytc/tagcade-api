<?php


namespace Tagcade\Bundle\AppBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Cache\Video\Refresher\VideoWaterfallTagCacheRefresherInterface;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;

class VideoWaterfallTagItemChangeListener
{
    /**
     * @var VideoWaterfallTagCacheRefresherInterface
     */
    private $cacheRefresher;
    /**
     * @var array
     */
    protected $changedVideoWaterfallTags;
    /**
     * @var array
     */
    protected $changedVideoWaterfallTagIds;

    function __construct(VideoWaterfallTagCacheRefresherInterface $cacheRefresher)
    {
        $this->cacheRefresher = $cacheRefresher;
        $this->changedVideoWaterfallTags = [];
        $this->changedVideoWaterfallTagIds = [];
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof VideoWaterfallTagItemInterface && ( $args->hasChangedField('strategy') || $args->hasChangedField('position')) ) {
            $id = $entity->getVideoWaterfallTag()->getId();
            if (!in_array($id, $this->changedVideoWaterfallTagIds)) {
                $this->changedVideoWaterfallTagIds[] = $id;
                $this->changedVideoWaterfallTags[] = $entity->getVideoWaterfallTag();
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof VideoWaterfallTagItemInterface) {
            return;
        }

        $id = $entity->getVideoWaterfallTag()->getId();
        if (!in_array($id, $this->changedVideoWaterfallTagIds)) {
            $this->changedVideoWaterfallTagIds[] = $id;
            $this->changedVideoWaterfallTags[] = $entity->getVideoWaterfallTag();
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preSoftDelete(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach($uow->getScheduledEntityDeletions() as $item) {
            if ($item instanceof VideoWaterfallTagItemInterface) {
                $id = $item->getVideoWaterfallTag()->getId();
                if (!in_array($id, $this->changedVideoWaterfallTagIds)) {
                    $this->changedVideoWaterfallTagIds[] = $id;
                    $this->changedVideoWaterfallTags[] = $item->getVideoWaterfallTag();
                }
            }
        }
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if (count($this->changedVideoWaterfallTags) < 1) {
            return;
        }

        foreach($this->changedVideoWaterfallTags as $videoWaterfallTag){
            $this->cacheRefresher->refreshVideoWaterfallTag($videoWaterfallTag);
        }

        $this->changedVideoWaterfallTagIds = [];
        $this->changedVideoWaterfallTags = [];
    }
}