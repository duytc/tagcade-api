<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Entity\Report\PerformanceReport\Display\AdNetwork\AdNetworkReport;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;

class AdNetworkReportRepository extends AbstractReportRepository implements AdNetworkReportRepositoryInterface
{
    public function getReportFor(AdNetworkInterface $adNetwork, DateTime $startDate, DateTime $endDate, $oneOrNull = false)
    {
        $qb = $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.adNetwork = :ad_network')
            ->setParameter('ad_network', $adNetwork);

        return $oneOrNull ? $qb->getQuery()->getOneOrNullResult() : $qb->getQuery()->getResult();
    }

    public function getReportForAllAdNetworkOfPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate, $oneOrNull = false)
    {
        $qb = $this->getReportsInRange($startDate, $endDate)
            ->join('r.adNetwork', 'nw')
            ->andWhere('nw.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher)
            ->groupBy('r.date');

        return $oneOrNull ? $qb->getQuery()->getOneOrNullResult() : $qb->getQuery()->getResult();
    }

    public function getPublisherAllPartnersByDay($publisherId, DateTime $startDate, DateTime $endDate)
    {
        $item = new AdNetworkReport();
        $item->setName('all partners');
        $item->setDate(new DateTime('today'));

        $qb = $this->createQueryBuilder('r');
        $qb->select('r.date, SUM(r.totalOpportunities) as totalOpportunities, SUM(r.estRevenue) as estRevenue, AVG(r.fillRate) as fillRate, AVG(r.estCpm) as estCpm, SUM(r.impressions) as impressions, SUM(r.passbacks) as passbacks')
            ->join('r.adNetwork', 'nw')
            ->where('nw.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisherId, Type::INTEGER)
            ->andWhere($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->andWhere($qb->expr()->isNotNull('nw.networkPartner'))
            ->groupBy('r.date');

        $results = $qb->getQuery()->getResult();

        $formattedResults = [];
        foreach ($results as $item) {
            $adNetworkReport = new AdNetworkReport();
            $adNetworkReport->setDate($item['date']);
            $adNetworkReport->setTotalOpportunities($item['totalOpportunities']);
            $adNetworkReport->setImpressions($item['impressions']);
            $adNetworkReport->setPassbacks($item['passbacks']);
            $adNetworkReport->setFillRate();
            $adNetworkReport->setEstRevenue($item['estRevenue']);
            $adNetworkReport->setEstCpm($item['estCpm']);

            $formattedResults[] = $adNetworkReport;
        }

        return $formattedResults;
    }
}