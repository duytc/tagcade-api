<?php


namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Tagcade\Model\Core\IvtPixelInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class IvtPixelRepository extends EntityRepository implements IvtPixelRepositoryInterface
{
    const SORT_FIELDS = [
        IvtPixelInterface::ID => IvtPixelInterface::ID,
        IvtPixelInterface::NAME => IvtPixelInterface::NAME,
        IvtPixelInterface::FIRE_ON => IvtPixelInterface::FIRE_ON,
        IvtPixelInterface::RUNNING_LIMIT => IvtPixelInterface::RUNNING_LIMIT,
    ];

    /**
     * @inheritdoc
     */
    public function getIvtPixelsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getIvtPixelsForPublisherQuery($publisher, $limit, $offset)
            ->addOrderBy('ivtp.' . IvtPixelInterface::NAME, 'asc');
        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getIvtPixelsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $publisherId = ($publisher instanceof SubPublisherInterface) ? $publisher->getPublisher()->getId() : $publisher->getId();

        $qb = $this->createQueryBuilder('ivtp')
            ->where('ivtp.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisherId, Type::INTEGER);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }

    /**
     * @inheritdoc
     */
    public function getIvtPixelForActivePublishers()
    {
        $qb = $this->createQueryBuilder('ivtp')
            ->leftJoin('ivtp.publisher', 'publisher')
            ->where('publisher.enabled = 1');

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getIvtPixelsForUserWithPagination(UserRoleInterface $user, PagerParam $param = null)
    {
        $qb = $this->createQueryBuilderForUser($user);

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like('ivtp.' . IvtPixelInterface::ID, ':searchKey'),
                    $qb->expr()->like('ivtp.' . IvtPixelInterface::NAME, ':searchKey'),
                    $qb->expr()->like('ivtp.' . IvtPixelInterface::FIRE_ON, ':searchKey'),
                    $qb->expr()->like('ivtp.' . IvtPixelInterface::RUNNING_LIMIT, ':searchKey')
                ))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), self::SORT_FIELDS)
        ) {
            switch ($param->getSortField()) {
                case self::SORT_FIELDS[IvtPixelInterface::ID]:
                    $qb->addOrderBy('ivtp.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case self::SORT_FIELDS[IvtPixelInterface::NAME]:
                    $qb->addOrderBy('ivtp.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case self::SORT_FIELDS[IvtPixelInterface::FIRE_ON]:
                    $qb->addOrderBy('ivtp.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case self::SORT_FIELDS[IvtPixelInterface::RUNNING_LIMIT]:
                    $qb->addOrderBy('ivtp.' . $param->getSortField(), $param->getSortDirection());
                    break;
                default:
                    break;
            }
        }

        return $qb;
    }

    /**
     * @param UserRoleInterface $user
     * @return QueryBuilder
     */
    private function createQueryBuilderForUser(UserRoleInterface $user)
    {
        return $user instanceof PublisherInterface ? $this->getIvtPixelsForPublisherQuery($user) : $this->createQueryBuilder('ivtp');
    }
}