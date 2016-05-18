<?php

namespace Tagcade\Bundle\AdminApiBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportEmailConfigInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class SourceReportEmailConfigRepository extends EntityRepository implements SourceReportEmailConfigRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getSourceReportEmailConfigForPublisher(PublisherInterface $publisher)
    {
        //step 1. query all SourceReportEmailConfig by PublisherId
        $qb = $this->createQueryBuilder('emCf')
            ->join('emCf.sourceReportSiteConfigs', 'stCf')
            ->join('stCf.site', 'st')
            ->where('st.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher->getId(), TYPE::INTEGER);

        /**
         * @var SourceReportEmailConfigInterface[] $result
         */
        $result = $qb->getQuery()->getResult();

        //step 2. remove all Sites which belong to other Publishers
        foreach ($result as $emailConfig) {
            $siteConfigs = $emailConfig->getSourceReportSiteConfigs();
            foreach ($siteConfigs as $idx => $siteConfig) {
                if ($publisher->getId() !== $siteConfig->getSite()->getPublisherId()) {
                    unset($siteConfigs[$idx]);
                }
            }
        }

        return $result;
    }

    /**
     * Get all active email config
     *
     * @return SourceReportEmailConfigInterface[]
     */
    public function getActiveConfig()
    {
        $qb = $this->createQueryBuilder('emCf')
            ->where('emCf.active = :active')
            ->setParameter('active', True, TYPE::BOOLEAN);

        return $qb->getQuery()->getResult();
    }
}