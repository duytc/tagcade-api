<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\Platform;


use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Repository\Report\VideoReport\Hierarchy\AbstractVideoReportRepository;


class VideoPublisherReportRepository extends AbstractVideoReportRepository implements VideoPublisherReportRepositoryInterface
{
    public function getSumVideoImpressionsForVideoPublisher(VideoPublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.impressions) as total')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->andWhere('r.videoPublisher = :publisher')
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->setParameter('publisher', $publisher)
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
    public function getReportsFor(VideoPublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->getReportsByDateRangeQuery($startDate, $endDate)
            ->andWhere('r.videoPublisher = :publisher')
            ->setParameter('publisher', $publisher);

        return $qb->getQuery()->getResult();
    }
}