<?php

namespace Tagcade\Bundle\AdminApiBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportEmailConfigInterface;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportSiteConfigInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class SourceReportSiteConfigRepository extends EntityRepository implements SourceReportSiteConfigRepositoryInterface
{

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function getSourceReportSiteConfigForPublisherAndEmailConfig(PublisherInterface $publisher, SourceReportEmailConfigInterface $emailConfig)
    {
        $qb = $this->createQueryBuilder('cf')
            ->join('cf.site', 'st')
            ->where('st.publisher = :publisher_id')
            ->andWhere('cf.sourceReportEmailConfig = :emailConfig')
            ->setParameter('publisher_id', $publisher->getId(), TYPE::INTEGER)
            ->setParameter('emailConfig', $emailConfig)
        ;

        return $qb->getQuery()->getResult();
    }


    /**
     * @inheritdoc
     */
    public function getSourceReportSiteConfigForEmailConfig(SourceReportEmailConfigInterface $emailConfig)
    {
        $qb = $this->createQueryBuilder('cf')
            ->join('cf.site', 'st')
            ->where('cf.sourceReportEmailConfig = :emailConfig')
            ->setParameter('emailConfig', $emailConfig)
        ;

        return $qb->getQuery()->getResult();
    }
}