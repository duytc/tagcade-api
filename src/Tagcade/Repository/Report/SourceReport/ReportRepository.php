<?php

namespace Tagcade\Repository\Report\SourceReport;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query;
use Doctrine\ORM\NoResultException;

use Tagcade\Entity\Report\SourceReport\Report;
use DateTime;

class ReportRepository extends EntityRepository implements ReportRepositoryInterface
{
    const DEFAULT_SORT_FIELD = 'visits';

    /**
     * @inheritdoc
     */
    public function getReports($siteId, DateTime $dateFrom, DateTime $dateTo = null, $rowOffset = null, $rowLimit = null, $sortField = null)
    {
        if (null == $dateTo) {
            $dateTo = $dateFrom;
        }

        $dql = '
            SELECT report.id FROM %s report
            WHERE report.siteId = :siteId
            AND report.date BETWEEN :dateFrom AND :dateTo
            ORDER BY report.date DESC
        ';

        $dql = sprintf($dql, Report::class);

        $query = $this->getEntityManager()->createQuery($dql);

        $query->setParameter('siteId', $siteId, Type::INTEGER);
        $query->setParameter('dateFrom', $dateFrom, Type::DATE);
        $query->setParameter('dateTo', $dateTo, Type::DATE);

        // converts to one dimensional array containing just the ids
        $sourceReportIds = array_map('current', $query->getScalarResult());

        $reports = [];

        foreach($sourceReportIds as $id) {
            if ($report = $this->getReport($id, $rowOffset, $rowLimit, $sortField)) {
                $reports[] = $report;
            }

            unset($id, $report);
        }

        if (empty($reports)) {
            return false;
        }

        return $reports;
    }

    /**
     * @inheritdoc
     */
    public function getReport($reportId, $rowOffset = null, $rowLimit = null, $sortField = null)
    {
        $allowedSortFields = [
            'visits',
            'pageViews',
            'displayOpportunities',
            'videoAdPlays',
            'videoAdImpressions',
        ];

        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = static::DEFAULT_SORT_FIELD;
        }

        $dql = '
            SELECT report, rec FROM %s report
            JOIN report.records rec
            WHERE report.id = :reportId
            ORDER BY rec.%s DESC
        ';

        $dql = sprintf($dql, Report::class, $sortField);

        $query = $this->getEntityManager()->createQuery($dql);

        if (is_int($rowOffset)) {
            $query->setFirstResult($rowOffset);
        }

        if (is_int($rowLimit)) {
            $query->setMaxResults($rowLimit);
        }

        $query->setParameter('reportId', $reportId, Type::INTEGER);

        try {
            return $query->getResult();
        }
        catch(NoResultException $e) {
            return false;
        }
    }
}