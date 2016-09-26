<?php


namespace Tagcade\Bundle\AppBundle\EventListener;


use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Cache\Video\Refresher\VideoWaterfallTagCacheRefresherInterface;
use Tagcade\Entity\Core\VideoDemandAdTag;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;

class VideoDemandPartnerChangeListener
{
    /**
     * @var VideoWaterfallTagCacheRefresherInterface
     */
    private $cacheRefresher;
    /**
     * @var array
     */
    protected $changedVideoWaterfallTags;
    protected $changedVideoWaterfallTagIds;
    /**
     * @var array
     */
    protected $newDemandAdTags;

    function __construct(VideoWaterfallTagCacheRefresherInterface $cacheRefresher)
    {
        $this->cacheRefresher = $cacheRefresher;
        $this->changedVideoWaterfallTags = [];
        $this->changedVideoWaterfallTagIds = [];
    }


    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof VideoDemandPartnerInterface && $args->hasChangedField('name')) {
            $videoDemandAdTagRepository = $args->getEntityManager()->getRepository(VideoDemandAdTag::class);
            $videoDemandAdTags = $videoDemandAdTagRepository->getVideoDemandAdTagsForDemandPartner($entity);
            /**
             * @var VideoDemandAdTagInterface $demandAdTag
             */
            foreach($videoDemandAdTags as $demandAdTag) {
                $id = $demandAdTag->getVideoWaterfallTagItem()->getVideoWaterfallTag()->getId();
                if (!in_array($id, $this->changedVideoWaterfallTagIds)) {
                    $this->changedVideoWaterfallTagIds[] = $id;
                    $this->changedVideoWaterfallTags[] = $demandAdTag->getVideoWaterfallTagItem()->getVideoWaterfallTag();
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