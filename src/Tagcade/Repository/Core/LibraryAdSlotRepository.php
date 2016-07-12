<?php

namespace Tagcade\Repository\Core;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Entity\Core\LibraryDisplayAdSlot;
use Tagcade\Entity\Core\LibraryDynamicAdSlot;
use Tagcade\Entity\Core\LibraryNativeAdSlot;
use Tagcade\Entity\Core\RonAdSlot;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class LibraryAdSlotRepository extends EntityRepository implements LibraryAdSlotRepositoryInterface
{
    protected $SORT_FIELDS = ['id' => 'id', 'size' => 'size', 'name' => 'name', 'publisher' => 'publisher', 'type' => 'type',
        'deployment' => 'deployment', 'ronAdSlot' => 'ronAdSlot'];

    /**
     * @inheritdoc
     */
    public function getLibraryAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getLibraryAdSlotsForPublisherQuery($publisher, $limit, $offset);

        return $qb->getQuery()->getResult();
    }


    public function getLibraryDisplayAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getLibraryAdSlotsForPublisherQuery($publisher, $limit, $offset);
        $qb->andWhere(sprintf('sl INSTANCE OF %s', LibraryDisplayAdSlot::class));

        return $qb->getQuery()->getResult();
    }

    public function getLibraryNativeAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getLibraryAdSlotsForPublisherQuery($publisher, $limit, $offset);
        $qb->andWhere(sprintf('sl INSTANCE OF %s', LibraryNativeAdSlot::class));

        return $qb->getQuery()->getResult();
    }

    public function getLibraryDynamicAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getLibraryAdSlotsForPublisherQuery($publisher, $limit, $offset);
        $qb->andWhere(sprintf('sl INSTANCE OF %s', LibraryDynamicAdSlot::class));

        return $qb->getQuery()->getResult();
    }

    /**
     * @param null $publisherId
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getAllLibraryAdSlotsUnusedInRon($publisherId = null, $limit = null, $offset = null)
    {
        $qb = $this->createGetLibraryAdSlotsQuery($limit, $offset);
        $qb->andWhere($qb->expr()->notIn('sl.id', $this->_em->createQueryBuilder()->select('identity(ron.libraryAdSlot)')->from(RonAdSlot::class, 'ron')->getDQL()));

        if (is_numeric($publisherId)) {
            $qb->andWhere('sl.publisher = :publisher_id')
                ->setParameter('publisher_id', $publisherId, Type::INTEGER);

        }

        return $qb->getQuery()->getResult();
    }

    public function getAllLibraryAdSlotsUsedInRon($publisherId = null, $limit = null, $offset = null)
    {
        $qb = $this->createGetLibraryAdSlotsQuery($limit, $offset);
        $qb->andWhere($qb->expr()->in('sl.id', $this->_em->createQueryBuilder()->select('identity(ron.libraryAdSlot)')->from(RonAdSlot::class, 'ron')->getDQL()));

        if (is_numeric($publisherId)) {
            $qb->andWhere('sl.publisher = :publisher_id')
                ->setParameter('publisher_id', $publisherId, Type::INTEGER);

        }

        return $qb->getQuery()->getResult();
    }

    public function getAllActiveLibraryAdSlots($limit = null, $offset = null)
    {
        $qb = $this->createGetLibraryAdSlotsQuery($limit, $offset);

        return $qb->getQuery()->getResult();
    }

    protected function createGetLibraryAdSlotsQuery($limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('sl')
            ->where('sl.visible = :visible')
            ->setParameter('visible', true, Type::BOOLEAN);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }

    /**
     * get Library AdSlots For Publisher Query
     *
     * IMPORTANT: only get all ad slots that are used for SHARING (visible = true), and default ORDER result by ad slot ID (for common use),
     * do not confuse and use wrong
     *
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getLibraryAdSlotsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getLibraryAdSlotsForPublisherQueryWithoutOrder($publisher)
            ->orderBy('sl.id', 'asc');

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }

    /**
     * get Library AdSlots For Publisher Query
     *
     * IMPORTANT: only get all ad slots that are used for SHARING (visible = true), and no ORDER result,
     * do not confuse and use wrong
     *
     * @param PublisherInterface $publisher
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getLibraryAdSlotsForPublisherQueryWithoutOrder(PublisherInterface $publisher)
    {
        return $this->createQueryBuilder('sl')
            ->where('sl.publisher = :publisher_id')
            ->andWhere('sl.visible = :visible')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER)
            ->setParameter('visible', true, Type::BOOLEAN);
    }

    /**
     * be carefully: READ docs from interface!!!
     *
     * @inheritdoc
     */
    public function getAllLibraryAdSlotsForPublisherQuery(PublisherInterface $publisher)
    {
        return $this->createQueryBuilder('sl')
            ->where('sl.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER);
    }

    /**
     * @inheritdoc
     */
    public function getLibraryAdSlotsWithPagination(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('sl')
            ->andWhere('sl.visible = :visible')
            ->setParameter('visible', true, Type::BOOLEAN);

        if ($user instanceof PublisherInterface) {
            // get all library ad slots that used for SHARING, without order
            $qb = $this->getLibraryAdSlotsForPublisherQueryWithoutOrder($user);
        }

        $qb->leftJoin('sl.publisher', 'pls');
        $qb->leftJoin('sl.ronAdSlot', 'rsl');

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb->andWhere($qb->expr()->orX($qb->expr()->like('sl.name', ':searchKey'), $qb->expr()->orX($qb->expr()->like('pls.company', ':searchKey'))))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            switch ($param->getSortField()) {
                case $this->SORT_FIELDS['id']:
                    $qb->addOrderBy('sl.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['name']:
                    $qb->addOrderBy('sl.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['publisher']:
                    $qb->addOrderBy('pls.' . 'company', $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['type']:
                    break;
                case $this->SORT_FIELDS['deployment']:
                    break;
                case $this->SORT_FIELDS['ronAdSlot']:
                    $qb->addOrderBy('rsl.' . 'visible', $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['size']:
                    break;
                default:
                    break;
            }
        }

        return $qb;
    }

    /**
     * @param $libraryAdSlotName
     * @return mixed|void
     */
    public function getLibraryAdSlotByName ($libraryAdSlotName)
    {
        $qb = $this->createQueryBuilder('sl')
            ->where('sl.name = :name')
            ->setParameter('name', $libraryAdSlotName);

        return $qb->getQuery()->getResult();
    }
} 