<?php

namespace Tagcade\Bundle\AdminApiBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Exception\InvalidArgumentException;
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
    public function getSourceReportSiteConfigForPublisherAndEmailConfig(PublisherInterface $publisher, $emailConfigId)
    {
        if (!is_int($emailConfigId)) {
            throw new InvalidArgumentException('expect integer for email config id');
        }

        $qb = $this->createQueryBuilder('cf')
            ->join('cf.site', 'st')
            ->where('st.publisher = :publisher_id')
            ->andWhere('cf.sourceReportEmailConfig = :emailConfig')
            ->setParameter('publisher_id', $publisher->getId(), TYPE::INTEGER)
            ->setParameter('emailConfig_id', $emailConfigId, TYPE::INTEGER)
        ;

        return $qb->getQuery()->getResult();
    }
}