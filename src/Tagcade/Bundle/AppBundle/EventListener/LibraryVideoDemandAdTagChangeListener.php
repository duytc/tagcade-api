<?php


namespace Tagcade\Bundle\AppBundle\EventListener;


use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Cache\Video\Refresher\VideoWaterfallTagCacheRefresherInterface;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;

class LibraryVideoDemandAdTagChangeListener
{
    /**
     * @var VideoWaterfallTagCacheRefresherInterface
     */
    private $cacheRefresher;
    /**
     * @var array
     */
    protected $changedWaterfallTags;
    /**
     * @var array
     */
    protected $changedWaterfallTagIds;

    function __construct(VideoWaterfallTagCacheRefresherInterface $cacheRefresher)
    {
        $this->cacheRefresher = $cacheRefresher;
        $this->changedWaterfallTags = [];
        $this->changedWaterfallTagIds = [];
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof LibraryVideoDemandAdTagInterface &&
            ($args->hasChangedField('videoDemandPartner') || $args->hasChangedField('tagURL') ||
                $args->hasChangedField(ExpressionInterface::TARGETING) || $args->hasChangedField('timeout')
            )
        ) {
            $demandAdTags = $entity->getVideoDemandAdTags();

            /** @var VideoDemandAdTagInterface $demandAdTag */
            foreach($demandAdTags as $demandAdTag) {
                if (!$demandAdTag->getVideoWaterfallTagItem() instanceof VideoWaterfallTagItemInterface) {
                    continue;
                }

                $id = $demandAdTag->getVideoWaterfallTagItem()->getVideoWaterfallTag()->getId();
                if (!in_array($id, $this->changedWaterfallTagIds)) {
                    $this->changedWaterfallTagIds[] = $id;
                    $this->changedWaterfallTags[] = $demandAdTag->getVideoWaterfallTagItem()->getVideoWaterfallTag();
                }
            }

            return;
        }
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if (count($this->changedWaterfallTags) < 1) {
            return;
        }

        foreach($this->changedWaterfallTags as $videoAdTag){
            $this->cacheRefresher->refreshVideoWaterfallTag($videoAdTag);
        }

        $this->changedWaterfallTagIds = [];
        $this->changedWaterfallTags = [];
    }
}