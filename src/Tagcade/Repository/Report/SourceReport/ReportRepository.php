<?php

namespace Tagcade\Repository\Report\SourceReport;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;
use Tagcade\Entity\Report\SourceReport\Report;
use DateTime;

class ReportRepository extends EntityRepository implements ReportRepositoryInterface
{
    const DEFAULT_SORT_FIELD = 'visits';

    /**
     * @inheritdoc
     */
    public function getReports($domain, DateTime $dateFrom, DateTime $dateTo = null, $rowOffset = null, $rowLimit = null, $sortField = null)
    {
        $allowedSortFields = ['visits', 'displayOpportunities', 'videoAdImpressions'];

        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = static::DEFAULT_SORT_FIELD;
        }

        $dql = '
            SELECT report, rec FROM %s report
            JOIN report.records rec
            WHERE report.site = :domain
            AND report.date = :date
            ORDER BY rec.visits DESC
        ';

        $dql = sprintf($dql, Report::class, $sortField);

        $query = $this->getEntityManager()->createQuery($dql);

        if (is_int($rowOffset)) {
            $query->setFirstResult((int) $rowOffset);
        }

        if (is_int($rowLimit)) {
            $query->setMaxResults((int) $rowLimit);
        }

        $query->setParameter('domain', $domain, Type::STRING);
        $query->setParameter('date', $dateFrom, Type::DATE);

        return $query->getResult();
    }
}