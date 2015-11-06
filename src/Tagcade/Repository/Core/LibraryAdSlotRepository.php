<?php

namespace Tagcade\Repository\Core;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Entity\Core\LibraryDisplayAdSlot;
use Tagcade\Entity\Core\LibraryDynamicAdSlot;
use Tagcade\Entity\Core\LibraryNativeAdSlot;
use Tagcade\Entity\Core\RonAdSlot;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class LibraryAdSlotRepository extends EntityRepository implements LibraryAdSlotRepositoryInterface
{
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
            ->setParameter('visible', true, Type::BOOLEAN)
        ;

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
    protected function getLibraryAdSlotsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('sl')
            ->where('sl.publisher = :publisher_id')
            ->andWhere('sl.visible = :visible')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER)
            ->setParameter('visible', true, Type::BOOLEAN)
            ->orderBy('sl.id', 'asc')
        ;

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }

    public function getAllLibraryAdSlotsForPublisherQuery(PublisherInterface $publisher)
    {
        return $this->createQueryBuilder('sl')
            ->where('sl.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER)
            ->orderBy('sl.id', 'asc')
        ;
    }
} 