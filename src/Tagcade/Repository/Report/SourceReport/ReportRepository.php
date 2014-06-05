<?php

namespace Tagcade\Repository\Report\SourceReport;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;
use DateTime;

class ReportRepository extends EntityRepository implements ReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReports($domain, DateTime $dateTo, DateTime $dateFrom = null, $rowLimit = -1)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT report, rec, tt, tk FROM Tagcade\Entity\Report\SourceReport\Report report
                JOIN report.records rec
                JOIN rec.trackingKeys tk
                JOIN tk.trackingTerm tt
                WHERE report.site = :domain
            ')
            ->setParameter('domain', $domain, Type::STRING)
            ->getResult()
        ;
    }
}