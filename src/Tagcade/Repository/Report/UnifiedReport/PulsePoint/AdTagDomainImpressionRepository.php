<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;


use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;

class AdTagDomainImpressionRepository extends AbstractReportRepository implements AdTagDomainImpressionRepositoryInterface
{
    protected function getReportsInRange(\DateTime $startDate, \DateTime $endDate)
    {
        $qb = parent::getReportsInRange($startDate, $endDate);

        return $qb
            ->addOrderBy('r.adTag', 'ASC')
            ->addOrderBy('r.domain', 'ASC')
            ->addOrderBy('r.date', 'ASC');
    }

    public function getReportWithDrillDown(PublisherInterface $publisher, $adTag = null, $date = null)
    {
        $qb = $this->createQueryBuilder('r');

        if (!is_null($adTag)) {
            $qb
                ->andWhere('r.adTag = :adTag')
                ->setParameter('adTag', $adTag);
        }

        if ($date instanceof \DateTime) {
            $qb
                ->andWhere('r.date = :date')
                ->setParameter('date', $date);
        }

        return $qb
            ->getQuery()
            ->getResult();
    }
}