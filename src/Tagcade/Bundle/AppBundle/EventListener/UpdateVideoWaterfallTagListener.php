<?php


namespace Tagcade\Bundle\AppBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Behaviors\ValidateVideoDemandAdTagAgainstPlacementRuleTrait;
use Tagcade\Cache\Video\Refresher\VideoWaterfallTagCacheRefresherInterface;
use Tagcade\Entity\Core\VideoDemandAdTag;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Core\WaterfallPlacementRuleInterface;
use Tagcade\Worker\Manager;

class UpdateVideoWaterfallTagListener
{
    use ValidateVideoDemandAdTagAgainstPlacementRuleTrait;

    /** @var VideoWaterfallTagCacheRefresherInterface */
    private $cacheRefresher;
    /** @var array */
    protected $changedVideoWaterfallTags;
    protected $changedVideoWaterfallTagIds;
    /** @var array */
    protected $newDemandAdTags;

    /**
     * @var Manager
     */
    protected $manager;
    function __construct(Manager $manager, VideoWaterfallTagCacheRefresherInterface $cacheRefresher)
    {
        $this->manager = $manager;
        $this->cacheRefresher = $cacheRefresher;
        $this->changedVideoWaterfallTags = [];
        $this->changedVideoWaterfallTagIds = [];
        $this->newDemandAdTags = [];
    }

    /**
     * handle postPersist event to determine new VideoWaterfallTags
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof VideoWaterfallTagInterface) {
            return;
        }

        $id = $entity->getId();
        if (!in_array($id, $this->changedVideoWaterfallTagIds)) {
            $this->changedVideoWaterfallTagIds[] = $id;
            $this->changedVideoWaterfallTags[] = $entity;
        }
    }

    /**
     * handle preUpdate event to determine updating VideoWaterfallTags
     *
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof VideoWaterfallTagInterface) {
            return;
        }

        if (
            $args->hasChangedField('platform')
            || $args->hasChangedField('adDuration')
            || $args->hasChangedField('targeting')
            || $args->hasChangedField('runOn')
        ) {
            $id = $entity->getId();
            if (!in_array($id, $this->changedVideoWaterfallTagIds)) {
                $this->changedVideoWaterfallTagIds[] = $id;
                $this->changedVideoWaterfallTags[] = $entity;
            }
        }

        if ($args->hasChangedField('buyPrice')) {
            $em = $args->getEntityManager();
            $this->autoPauseVideoDemandAdTags($em, $entity);
        }
    }

    protected function autoPauseVideoDemandAdTags(EntityManagerInterface $em, VideoWaterfallTagInterface $waterfallTag)
    {
        $autoPauseTags = [];
        $autoActiveTags = [];
        $videoDemandAdTagRepository = $em->getRepository(VideoDemandAdTag::class);
        $videoDemandAdTags = $videoDemandAdTagRepository->getVideoDemandAdTagsForVideoWaterfallTag($waterfallTag);
        /** @var VideoDemandAdTagInterface $videoDemandAdTag */
        foreach($videoDemandAdTags as $videoDemandAdTag) {
            if (!$videoDemandAdTag->getWaterfallPlacementRule() instanceof WaterfallPlacementRuleInterface) {
                continue;
            }

            if ($this->validateDemandAdTagAgainstPlacementRule($videoDemandAdTag) === false) {
                $autoPauseTags[] = $videoDemandAdTag->getId();
            } else {
                $autoActiveTags[] = $videoDemandAdTag->getId();
            }
        }

        $this->manager->autoPauseVideoDemandAdTags($autoPauseTags);
        $this->manager->autoActiveVideoDemandAdTags($autoActiveTags);
    }

    /**
     * handle preUpdate event to determine deleting VideoWaterfallTags
     *
     * @param LifecycleEventArgs $args
     */
    public function preSoftDelete(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityDeletions() as $item) {
            if ($item instanceof VideoWaterfallTagInterface) {
                $this->cacheRefresher->removeVideoWaterfallTagCache($item);
            }
        }
    }

    /**
     * handle postFlush event to update video cache for VideoWaterfallTags
     *
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        if (count($this->changedVideoWaterfallTags) < 1) {
            return;
        }

        foreach ($this->changedVideoWaterfallTags as $videoWaterfallTag) {
            $this->cacheRefresher->refreshVideoWaterfallTag($videoWaterfallTag);
        }

        $this->changedVideoWaterfallTagIds = [];
        $this->changedVideoWaterfallTags = [];
        $this->newDemandAdTags = [];
    }
}