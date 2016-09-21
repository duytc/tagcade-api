<?php


namespace Tagcade\Repository\Core;


use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class LibraryVideoDemandAdTagRepository extends EntityRepository implements LibraryVideoDemandAdTagRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getLibraryVideoDemandAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('r')
            ->join('r.videoDemandPartner', 'vdp')
            ->where('vdp.publisher = :publisher')
            ->setParameter('publisher', $publisher);

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
    public function getLibraryVideoDemandAdTagsForDemandPartner(VideoDemandPartnerInterface $videoDemandPartner, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('lvdt')
            ->where('lvdt.videoDemandPartner = :videoDemandPartner')
            ->setParameter('videoDemandPartner', $videoDemandPartner);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }
}