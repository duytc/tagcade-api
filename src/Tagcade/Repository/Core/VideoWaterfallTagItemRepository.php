<?php


namespace Tagcade\Repository\Core;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\VideoWaterfallTag;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class VideoWaterfallTagItemRepository extends EntityRepository implements VideoWaterfallTagItemRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagItemsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('vti')
            ->leftJoin('vti.videoWaterfallTag', 'vt')
            ->where('vt.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagItemsForAdTag(VideoWaterfallTagInterface $videoWaterfallTag, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('vti')
            ->where('vti.videoWaterfallTag = :video_waterfall_tag_id')
            ->setParameter('video_waterfall_tag_id', $videoWaterfallTag->getId(), Type::INTEGER);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function getMaxPositionInWaterfallTag(VideoWaterfallTagInterface $waterfallTag)
    {
        return $this->createQueryBuilder('r')
            ->select('MAX(r.position)')
            ->where('r.videoWaterfallTag = :waterfall')
            ->setParameter('waterfall', $waterfallTag)
            ->getQuery()->getSingleScalarResult();
    }

    public function getWaterfallTagItemWithPositionInWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag, $position)
    {
        return $this->createQueryBuilder('r')
            ->where('r.videoWaterfallTag = :waterfall')
            ->andWhere('r.position = :position')
            ->setParameter('position', $position, Type::INTEGER)
            ->setParameter('waterfall', $videoWaterfallTag)
            ->getQuery()->getOneOrNullResult()
        ;
    }
}