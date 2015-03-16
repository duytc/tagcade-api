<?php

namespace Tagcade\Bundle\AdminApiBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\User\Role\PublisherInterface;

class SourceReportEmailConfigRepository extends EntityRepository implements SourceReportEmailConfigRepositoryInterface
{

    public function getSourceReportEmailConfigForPublisher(PublisherInterface $publisher)
    {
        $qb = $this->createQueryBuilder('emCf')
            ->join('emCf.sourceReportSiteConfigs', 'stCf')
            ->join('stCf.site', 'st')
            ->where('st.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher->getId(), TYPE::INTEGER)
        ;

        return $qb->getQuery()->getResult();
    }

} 