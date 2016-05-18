<?php


namespace Tagcade\Repository\Report\UnifiedReport;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;

abstract class AbstractReportRepository extends EntityRepository
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    const BATCH_SIZE = 50;
    protected function getReportsInRange(\DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        return $qb
            ->andWhere($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE);
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}