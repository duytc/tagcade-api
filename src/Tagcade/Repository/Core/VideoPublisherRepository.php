<?php


namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class VideoPublisherRepository extends EntityRepository implements VideoPublisherRepositoryInterface
{
    protected $SORT_FIELDS = [
        'id' => 'publisher_id',
        'name' => 'name',
        'company' => 'company',
    ];
    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getVideoPublishersForPublisherWithPagination(UserRoleInterface $user, PagerParam $param){
        $qb = $this->createQueryBuilder('vp');
        if ($user instanceof PublisherInterface) {
            $qb
                ->where('vp.publisher = :publisher_id')
                ->setParameter('publisher_id', $user->getId(), Type::INTEGER);
        }

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like('vp.publisher', ':searchKey'),
                    $qb->expr()->like('vp.name', ':searchKey'),
                    $qb->expr()->like('vp.id', ':searchKey')
                ))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC'])&&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            $qb->addOrderBy('vp.' . $param->getSortField(), $param->getSortDirection());
        }
        return $qb;
    }
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