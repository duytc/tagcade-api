<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;
use Tagcade\Model\User\Role\PublisherInterface;

class AccountReportRepository extends AbstractReportRepository implements AccountReportRepositoryInterface
{
    public function getReportFor(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.publisher = :publisher')
            ->setParameter('publisher', $publisher->getUser())
            ->getQuery()
            ->getResult()
        ;
    }
}