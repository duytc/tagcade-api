<?php

namespace Tagcade\Repository\Core;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class LibraryAdTagRepository extends EntityRepository implements LibraryAdTagRepositoryInterface
{
    protected $SORT_FIELDS = [
        'id' => 'id',
        'name' => 'name',
        'adNetworkName' => 'adNetwork.name',
        'publisherCompany' => 'adNetwork.publisher.company',
        'associatedTagCount' => 'associatedTagCount'
    ];

    /**
     * @inheritdoc
     */
    public function getLibraryAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getLibraryAdTagsForPublisherQuery($publisher, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getLibraryAdTagsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('tl')
            ->join('tl.adNetwork', 'nw')
            ->where('nw.publisher = :publisher_id')
            ->andWhere('tl.visible = :visible')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER)
            ->setParameter('visible', true, Type::INTEGER)
            ->orderBy('tl.id', 'asc');

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }

    public function getLibraryAdTagsWithPagination(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('lat')
            ->where('lat.visible = :visible')
            ->setParameter('visible', true, Type::BOOLEAN);

        if ($user instanceof PublisherInterface || (is_int($param->getPublisherId()) && $param->getPublisherId() > 0)) {
            // get all library ad slots that used for SHARING, without order
            $qb
                ->join('lat.adNetwork', 'nw')
                ->andWhere('nw.publisher = :publisher_id');
            if ($user instanceof PublisherInterface) {
                $qb->setParameter('publisher_id', $user->getId(), Type::INTEGER);
            } else {
                $qb->setParameter('publisher_id', $param->getPublisherId(), Type::INTEGER);
            }
        } else {
            $qb->join('lat.adNetwork', 'nw')
                ->join('nw.publisher', 'pls');
        }

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('lat.name', ':searchKey'),
                $qb->expr()->like('lat.id', ':searchKey'),
                $qb->expr()->like('nw.name', ':searchKey')
            ))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {

            switch ($param->getSortField()) {
                case $this->SORT_FIELDS['id']:
                    $qb->addOrderBy('lat.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['name']:
                    $qb->addOrderBy('lat.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['adNetworkName']:
                    $qb->addOrderBy('nw.name', $param->getSortDirection());
                    break;
                default:
                    break;
            }
        }

        return $qb;
    }

    public function getLibraryAdTagsForLibraryAdSlot(BaseLibraryAdSlotInterface $libraryAdSlot, $limit = null, $offset = null)
    {
        return $this->createQueryBuilder('t')
            ->join('t.libSlotTags', 'slt')
            ->where('slt.libraryAdSlot = :librarySlot')
            ->setParameter('librarySlot', $libraryAdSlot)
            ->getQuery()
            ->getResult();
    }


    /**
     * @param $htmlValue
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getLibraryAdTagsByHtml($htmlValue, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('tl')
            ->andWhere('tl.html = :htmlValue')
            ->setParameter('htmlValue', $htmlValue);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }
        return $qb->getQuery()->getResult();
    }

    public function getLibraryAdTagHasExpressionDescriptor()
    {
        return $this->createQueryBuilder('lat')
            ->where('lat.expressionDescriptor IS NOT NULL')
            ->getQuery()
            ->getResult();
    }
}