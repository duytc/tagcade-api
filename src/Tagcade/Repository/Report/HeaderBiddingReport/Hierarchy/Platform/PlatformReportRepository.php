<?php


namespace Tagcade\Repository\Report\HeaderBiddingReport\Hierarchy\Platform;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform\PlatformReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportInterface;
use Tagcade\Repository\Report\HeaderBiddingReport\AbstractReportRepository;

class PlatformReportRepository extends AbstractReportRepository implements PlatformReportRepositoryInterface
{
    public function getReportFor(DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->getQuery()
            ->getResult()
        ;
    }

    public function overrideReport(PlatformReportInterface $report)
    {
        $sql = 'INSERT INTO `report_header_bidding_display_hierarchy_platform`
                 (date, requests, billed_rate, billed_amount)
                 VALUES (:date, :requests, :billedRate, :billedAmount)
                 ON DUPLICATE KEY UPDATE
                 requests = :requests,
                 billed_amount = :billedAmount,
                 billed_rate = :billedRate
                 ';

        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->prepare($sql);

        $qb->bindValue('date', $report->getDate(), Type::DATE);
        $qb->bindValue('billedRate', $report->getBilledRate() !== null ? $report->getBilledRate() : 0);
        $qb->bindValue('billedAmount', $report->getBilledAmount() !== null ? $report->getBilledAmount() : 0);
        $qb->bindValue('requests', $report->getRequests() !== null ? $report->getRequests() : 0);

        $connection->beginTransaction();
        try {
            if (false === $qb->execute()) {
                throw new \Exception('Execute error');
            }
            $connection->commit();
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }
}