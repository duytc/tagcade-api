<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\IvtPixelInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class IvtPixelWaterfallTagRepository extends EntityRepository implements IvtPixelWaterfallTagRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getIvtPixelWaterfallTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('ipwt')
            ->leftJoin('ipwt.ivtPixel', 'ip')
            ->where('ip.publisher = :publisher_id')
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
    public function getIvtPixelWaterfallTagsByIvtPixel(IvtPixelInterface $ivtPixel, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('ipwt')
            ->where('ipwt.ivtPixel = :ivtPixel')
            ->setParameter('ivtPixel', $ivtPixel);

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
    public function getIvtPixelWaterfallTagsByWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('ipwt')
            ->where('ipwt.waterfallTag = :waterfallTag')
            ->setParameter('waterfallTag', $videoWaterfallTag);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }
}