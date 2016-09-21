<?php


namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Entity\Core\VideoDemandPartner;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class VideoDemandPartnerRepository extends EntityRepository implements VideoDemandPartnerRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getVideoDemandPartnersForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('vdm')
            ->where('vdm.publisher = :publisher_id')
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
    public function getVideoDemandPartnerForPublisherByCanonicalName(PublisherInterface $publisher, $canonicalName)
    {
        $qb = $this->createQueryBuilder('vdm')
            ->where('vdm.publisher = :publisher_id')
            ->andWhere('vdm.nameCanonical = :canonicalName')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER)
            ->setParameter('canonicalName', $canonicalName);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getVideoDemandPartnersByFilterParams(FilterParameterInterface $filterParameter)
    {
        $qb = $this->createQueryBuilder('vdm');

        if ($filterParameter->getPublishers() != null) {
            $qb
                ->andWhere($qb->expr()->in('vdm.publisher', $filterParameter->getPublishers()));
        }

        // we get all videoDemandPartners by either publishers or demandPartners
        if ($filterParameter->getVideoDemandPartners() != null) {
            $qb
                ->andWhere($qb->expr()->in('vdm.id', $filterParameter->getVideoDemandPartners()));
        }

        return $qb->getQuery()->getResult();
    }
}