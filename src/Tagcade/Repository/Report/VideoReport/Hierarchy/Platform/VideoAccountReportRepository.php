<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\Platform;


use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\VideoReport\Hierarchy\AbstractVideoReportRepository;


class VideoAccountReportRepository extends AbstractVideoReportRepository implements VideoAccountReportRepositoryInterface
{
    public function getSumVideoImpressionsForPublisher(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.impressions) as total')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->andWhere('r.publisher = :publisher')
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->setParameter('publisher', $publisher->getUser())
            ->getQuery()
            ->getSingleScalarResult()
        ;

        if (null === $result) {
            return 0;
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getReportsFor(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->getReportsByDateRangeQuery($startDate, $endDate)
            ->andWhere('r.publisher = :publisher')
            ->setParameter('publisher', $publisher);

        return $qb->getQuery()->getResult();
    }

    public function getAggregatedReportsByDateRange(array $publisherIds, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->getReportsByDateRangeQuery($startDate, $endDate);
        $qb->andWhere($qb->expr()->in('r.publisher', $publisherIds));

        $qb->select('
            SUM(r.requests) as requests,
            SUM(r.bids) as bids,
            SUM(r.errors) as errors,
            SUM(r.impressions) as impressions,
            SUM(r.clicks) as clicks,
            SUM(r.adTagRequests) as adTagRequests,
            SUM(r.adTagBids) as adTagBids,
            SUM(r.adTagErrors) as adTagErrors,
            SUM(r.billedAmount) as billedAmount,
            SUM(r.blocks) as blocks,
            SUM(r.estDemandRevenue) as estDemandRevenue,
            SUM(r.estSupplyCost) as estSupplyCost
            '
        );

        return current($qb->getQuery()->getArrayResult());
    }
}