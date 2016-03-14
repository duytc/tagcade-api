<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class SegmentRepository extends EntityRepository implements SegmentRepositoryInterface
{
    const TYPE_SEGMENT_CUSTOM = 'custom';
    const TYPE_SEGMENT_SUB_PUBLISHER = 'publisher';

    /**
     * @inheritdoc
     */
    public function getSegmentsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb =  $this->createSegmentForPublisherQueryBuilder($publisher, $limit, $offset);

        return $qb->getQuery()->getResult();
    }


    /**
     * @inheritdoc
     */
    public function getSegmentsByTypeForPublisher(PublisherInterface $publisher, $type = null, $limit = null, $offset = null)
    {
        $qb = $this->createSegmentForPublisherQueryBuilder($publisher, $limit, $offset);

        switch ($type) {
            case self::TYPE_SEGMENT_CUSTOM:
                $qb->andWhere('s.subPublisher = null');
                break;
            case self::TYPE_SEGMENT_SUB_PUBLISHER:
                $qb->andWhere('s.subPublisher != null');
                break;
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    protected function createSegmentForPublisherQueryBuilder(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher->getId(), TYPE::INTEGER);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return$qb;
    }

}