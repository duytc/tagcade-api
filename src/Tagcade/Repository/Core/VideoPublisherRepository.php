<?php


namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class VideoPublisherRepository extends EntityRepository implements VideoPublisherRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getVideoPublishersForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('vp')
            ->where('vp.publisher = :publisher_id')
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
    public function getVideoPublishersByFilterParams(FilterParameterInterface $filterParameter)
    {
        $qb = $this->createQueryBuilder('vp');

        if (!empty($filterParameter->getPublishers())) {
            $qb->andWhere($qb->expr()->in('vp.publisher', $filterParameter->getPublishers()));
        }

        if (!empty($filterParameter->getVideoPublishers())) {
            $qb->andWhere($qb->expr()->in('vp.id', $filterParameter->getVideoPublishers()));
        }

        return $qb->getQuery()->getResult();
    }
}