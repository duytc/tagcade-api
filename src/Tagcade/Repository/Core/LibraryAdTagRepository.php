<?php

namespace Tagcade\Repository\Core;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class LibraryAdTagRepository extends EntityRepository implements LibraryAdTagRepositoryInterface{

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
            ->orderBy('tl.id', 'asc')
        ;

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
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
    public function getLibraryAdTagsByHtml($htmlValue,  $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('tl')
                    ->andWhere('tl.html = :htmlValue')
                    ->setParameter('htmlValue',$htmlValue);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }
        return $qb->getQuery()->getResult();
    }
}