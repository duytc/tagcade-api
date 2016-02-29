<?php


namespace Tagcade\Repository\Report\RtbReport\Hierarchy;

use DateTime;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\RtbReport\AbstractReportRepository;

class AccountReportRepository extends AbstractReportRepository implements AccountReportRepositoryInterface
{
    public function getReportFor(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRangeQuery($startDate, $endDate)
            ->andWhere('r.publisher = :publisher')
            ->setParameter('publisher', $publisher->getUser())
            ->getQuery()
            ->getResult()
        ;
    }
}