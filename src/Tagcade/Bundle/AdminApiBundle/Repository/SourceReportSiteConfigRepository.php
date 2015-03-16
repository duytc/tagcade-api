<?php

namespace Tagcade\Bundle\AdminApiBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\User\Role\PublisherInterface;

class SourceReportSiteConfigRepository extends EntityRepository implements SourceReportSiteConfigRepositoryInterface
{

    public function getSourceReportSiteConfigForPublisher(PublisherInterface $publisher)
    {
        $qb = $this->createQueryBuilder('cf')
            ->join('cf.site', 'st')
            ->where('st.publisher = :publisher_id')
            ->groupBy('cf.email')
            ->setParameter('publisher_id', $publisher->getId(), TYPE::INTEGER)
        ;

        return $qb->getQuery()->getResult();
    }

} 