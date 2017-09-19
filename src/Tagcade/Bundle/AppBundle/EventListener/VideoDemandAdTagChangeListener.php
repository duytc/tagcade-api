<?php


namespace Tagcade\Bundle\AppBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Cache\Video\Refresher\VideoWaterfallTagCacheRefresherInterface;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;

class VideoDemandAdTagChangeListener
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

        if ($entity instanceof VideoDemandAdTagInterface &&
            (
              $args->hasChangedField('priority') || $args->hasChangedField('rotationWeight') || $args->hasChangedField('targetingOverride') || $args->hasChangedField(ExpressionInterface::TARGETING) ||
              $args->hasChangedField('active') || $args->hasChangedField('videoWaterfallTagItem') || $args->hasChangedField('timeout')
            )
        ) {
            $id = $entity->getVideoWaterfallTagItem()->getVideoWaterfallTag()->getId();
            if (!in_array($id, $this->changedVideoWaterfallTagIds)) {
                $this->changedVideoWaterfallTagIds[] = $id;
                $this->changedVideoWaterfallTags[] = $entity->getVideoWaterfallTagItem()->getVideoWaterfallTag();
            }

            return;
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof VideoDemandAdTagInterface) {
            return;
        }

        $videoWaterfallTagItem = $entity->getVideoWaterfallTagItem();
        if ($videoWaterfallTagItem instanceof VideoWaterfallTagItemInterface) {
            $id = $videoWaterfallTagItem->getVideoWaterfallTag()->getId();
            if (!in_array($id, $this->changedVideoWaterfallTagIds)) {
                $this->changedVideoWaterfallTagIds[] = $id;
                $this->changedVideoWaterfallTags[] = $videoWaterfallTagItem->getVideoWaterfallTag();
            }
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
            if ($item instanceof VideoDemandAdTagInterface) {
                $id = $item->getVideoWaterfallTagItem()->getVideoWaterfallTag()->getId();
                if (!in_array($id, $this->changedVideoWaterfallTagIds)) {
                    $this->changedVideoWaterfallTagIds[] = $id;
                    $this->changedVideoWaterfallTags[] = $item->getVideoWaterfallTagItem()->getVideoWaterfallTag();
                }

                /**
                 * @var VideoWaterfallTagItemInterface $videoWaterfallTagItem
                 */
                $videoWaterfallTagItem = $item->getVideoWaterfallTagItem();
                if (count($videoWaterfallTagItem->getVideoDemandAdTags()) <= 1) {
                    $videoWaterfallTagItem->setDeletedAt(new \DateTime('today'));
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