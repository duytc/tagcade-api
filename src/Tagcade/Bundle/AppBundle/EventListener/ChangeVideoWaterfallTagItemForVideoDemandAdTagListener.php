<?php


namespace Tagcade\Bundle\AppBundle\EventListener;


use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Entity\Core\VideoWaterfallTagItem;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;

class ChangeVideoWaterfallTagItemForVideoDemandAdTagListener
{
    /**
     * @var null|VideoWaterfallTagItemInterface
     */
    protected $oldVideoWaterfallTagItem;

    function __construct()
    {
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof VideoDemandAdTagInterface ||
            ($entity instanceof VideoDemandAdTagInterface && !$args->hasChangedField('videoWaterfallTagItem'))
        ) {
            return;
        }

        $this->oldVideoWaterfallTagItem = $args->getOldValue('videoWaterfallTagItem');
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        if (!$this->oldVideoWaterfallTagItem instanceof VideoWaterfallTagItemInterface) {
            return;
        }

        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        if (count($this->oldVideoWaterfallTagItem->getVideoDemandAdTags()) > 0) {
            return;
        }

        $this->oldVideoWaterfallTagItem->setDeletedAt(new \DateTime('today'));
        $metaData = $em->getClassMetadata(VideoWaterfallTagItem::class);
        $uow->recomputeSingleEntityChangeSet($metaData, $this->oldVideoWaterfallTagItem);
    }
}