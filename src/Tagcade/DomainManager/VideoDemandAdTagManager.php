<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Entity\Core\VideoDemandAdTag;
use Tagcade\Entity\Core\VideoWaterfallTagItem;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Repository\Core\VideoDemandAdTagRepositoryInterface;

class VideoDemandAdTagManager implements VideoDemandAdTagManagerInterface
{
    protected $batchSize;
    /** @var ObjectManager */
    protected $om;
    /** @var VideoDemandAdTagRepositoryInterface */
    protected $repository;

    public function __construct(ObjectManager $om, VideoDemandAdTagRepositoryInterface $repository, $batchSize)
    {
        $this->om = $om;
        $this->repository = $repository;
        $this->batchSize = $batchSize;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, VideoDemandAdTagInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $videoDemandAdTag)
    {
        if (!$videoDemandAdTag instanceof VideoDemandAdTagInterface) throw new InvalidArgumentException('expect VideoDemandAdTagInterface object');

        $videoWaterfallTagItem = $videoDemandAdTag->getVideoWaterfallTagItem();

        if (null == $videoWaterfallTagItem->getId()) {
            $this->saveForNewVideoWaterfallTagItem($videoDemandAdTag);
        } else {
            $this->saveForExistingVideoWaterfallTagItem($videoDemandAdTag);
        }

        //$this->om->persist($videoDemandAdTag);
        //$this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $videoDemandAdTag)
    {
        if (!$videoDemandAdTag instanceof VideoDemandAdTagInterface) throw new InvalidArgumentException('expect VideoDemandAdTagInterface object');

        $this->om->remove($videoDemandAdTag);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function createNew()
    {
        $entity = new ReflectionClass($this->repository->getClassName());
        return $entity->newInstance();
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->repository->getAll($limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getVideoDemandAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getVideoDemandAdTagsForPublisher($publisher, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getVideoDemandAdTagsForVideoWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag, $limit = null, $offset = null)
    {
        return $this->repository->getVideoDemandAdTagsForVideoWaterfallTag($videoWaterfallTag, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getVideoDemandAdTagsForDemandPartner(VideoDemandPartnerInterface $videoDemandPartner, $limit = null, $offset = null)
    {
        return $this->repository->getVideoDemandAdTagsForDemandPartner($videoDemandPartner, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getVideoDemandAdTagsNotBelongToVideoTagItem(UserRoleInterface $user, $limit = null, $offset = null)
    {
        return $this->repository->getVideoDemandAdTagsNotBelongToVideoTagItem($user, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getVideoDemandAdTagsForLibraryVideoDemandAdTag(LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag, $limit = null, $offset = null)
    {
        return $this->repository->getVideoDemandAdTagsForLibraryVideoDemandAdTag($libraryVideoDemandAdTag, $limit, $offset);
    }

    /**
     * @param ModelInterface $site
     * @return mixed|void
     */
    public function persists(ModelInterface $site)
    {
        $this->om->persist($site);
    }

    /**
     * Flush message to data base
     */
    public function flush()
    {
        $this->om->flush();
    }

    /**
     * create new Video Waterfall Tag Item
     *
     * @param VideoDemandAdTagInterface $videoDemandAdTag
     */
    public function saveForNewVideoWaterfallTagItem(VideoDemandAdTagInterface $videoDemandAdTag)
    {
        $videoWaterfallTagItem = $videoDemandAdTag->getVideoWaterfallTagItem();
        $videoWaterfallTag = $videoWaterfallTagItem->getVideoWaterfallTag();

        // sure again that videoDemandAdTagItem is new
        if (null != $videoWaterfallTagItem->getId()) {
            return;
        }

        /* @var VideoWaterfallTagItemInterface[] $oldVideoWaterfallTagItems */
        $oldVideoWaterfallTagItems = $videoWaterfallTag->getVideoWaterfallTagItems();

        // calculate last position
        $waterfallTagItemRepository = $this->om->getRepository(VideoWaterfallTagItem::class);
        $lastPosition = (int)$waterfallTagItemRepository->getMaxPositionInWaterfallTag($videoWaterfallTag);

        // calculate positions,
        // - if position == null or greater than or equal last position => auto last position
        // - if specified position and less than last position => auto shift down
        if (null === $videoWaterfallTagItem->getPosition()
            || $lastPosition <= $videoWaterfallTagItem->getPosition()
        ) {
            $videoWaterfallTagItem->setPosition($lastPosition + 1);
            $videoWaterfallTagItem->setVideoDemandAdTags([$videoDemandAdTag]);
        } else {
            // support "shift down" feature
            foreach ($oldVideoWaterfallTagItems as $oldVideoWaterfallTagItem) {
                // skip old positions less than shift down position
                if ($oldVideoWaterfallTagItem->getPosition() < $videoWaterfallTagItem->getPosition()) {
                    continue;
                }

                // shift down old positions greater than or equal shift down position
                $oldVideoWaterfallTagItem->setPosition($oldVideoWaterfallTagItem->getPosition() + 1);
            }

            $videoWaterfallTagItem->setVideoDemandAdTags([$videoDemandAdTag]);

            $oldVideoWaterfallTagItems[] = $videoWaterfallTagItem;
            $videoWaterfallTag->setVideoWaterfallTagItems($oldVideoWaterfallTagItems);
        }

        $this->om->persist($videoWaterfallTagItem);
        $this->om->flush();
    }

    /**
     * update existing Video Waterfall Tag Item
     *
     * @param VideoDemandAdTagInterface $videoDemandAdTag
     */
    public function saveForExistingVideoWaterfallTagItem(VideoDemandAdTagInterface $videoDemandAdTag)
    {
        $videoWaterfallTagItem = $videoDemandAdTag->getVideoWaterfallTagItem();

        // sure again that videoDemandAdTagItem is existing
        if (null == $videoWaterfallTagItem->getId()) {
            return;
        }

        // add VideoDemandAdTag to VideoWaterfallTagItem, i.e if old videoWaterfallTagItems has videoDemandAdTags as 1,2 => updated to 1,2,n
        $videoWaterfallTagItem->addVideoDemandAdTag($videoDemandAdTag);

        $this->om->persist($videoWaterfallTagItem);
        $this->om->flush();
    }

    public function updateVideoDemandAdTagForDemandPartner(VideoDemandPartnerInterface $demandPartner, $active = false, $waterfall = null)
    {
        $qb = $this->om->getRepository(VideoDemandAdTag::class)->createQueryBuilder('vda')
            ->join('vda.libraryVideoDemandAdTag', 'lvda')
            ->where('lvda.videoDemandPartner = :demand_partner')
            ->setParameter('demand_partner', $demandPartner);

        if ($waterfall instanceof VideoWaterfallTagInterface) {
            $qb->join('vda.videoWaterfallTagItem', 'item')
                ->andWhere('item.videoWaterfallTag = :waterfall_tag')
                ->setParameter('waterfall_tag', $waterfall);
        }

        $it = $qb->getQuery()->iterate();

        $count = 0;
        /** @var VideoDemandAdTagInterface $demandAdTag */
        foreach ($it as $row) {
            $demandAdTag = $row[0];
            if ($demandAdTag->getActive() !== $active) {
                $demandAdTag->setActive($active);
                $this->om->persist($demandAdTag);
                $count++;
            }

            if ($count % $this->batchSize === 0 && $count > 0) {
                $this->om->flush();
                $this->om->clear();
            }
        }

        $this->om->flush();
        $this->om->clear();
    }

    public function getVideoDemandAdTagsHaveRequestCapByStatus($status)
    {
        return $this->repository->getVideoDemandAdTagsHaveRequestCapByStatus($status);
    }

    public function getVideoDemandAdTagsByStatus($status)
    {
        return $this->repository->getVideoDemandAdTagsByStatus($status);
    }
}