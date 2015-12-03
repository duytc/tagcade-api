<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;


use Tagcade\Model\Report\UnifiedReport\PulsePoint\AdTagDomainImpression;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;

class AdTagDomainImpressionRepository extends AbstractReportRepository implements AdTagDomainImpressionRepositoryInterface
{
    public function getReportFor(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate)
    {
        $qb = parent::getReportsInRange($startDate, $endDate);

        $result = $qb
            ->andWhere('r.publisherId = :publisherId')
            ->setParameter('publisherId', $publisher->getId())
            ->addSelect('r.id')
            ->addSelect('r.publisherId')
            ->addSelect('r.domain')
            ->addSelect('SUM(r.totalImps) as totalImps')
            ->addSelect('SUM(r.paidImps) as paidImps')
            ->addSelect('(SUM(r.fillRate * r.paidImps) / SUM(r.paidImps)) as fillRate')
            ->addSelect('r.domainStatus')
            ->addSelect('r.adTag')
            ->addSelect('r.adTagId')
            ->addSelect('r.date')
            ->addGroupBy('r.adTagId, r.domain')
            ->getQuery()
            ->getResult();

        // TODO: get result as array of DailyCountry objects, not mixed array ([Original AdTagDomainImpression, id, publisherId, ...]
        if (is_array($result)) {
            $result = array_map(function ($rst) {
                return (is_array($rst) && count($rst) > 10)
                    ? (new AdTagDomainImpression())
                        ->setId($rst['id'])
                        ->setPublisherId($rst['publisherId'])
                        ->setDomain($rst['domain'])
                        ->setTotalImps($rst['totalImps'])
                        ->setPaidImps($rst['paidImps'])
                        ->setFillRate($rst['fillRate'])
                        ->setDomainStatus($rst['domainStatus'])
                        ->setAdTag($rst['adTag'])
                        ->setAdTagId($rst['adTagId'])
                        ->setDate($rst['date'])
                    : null;
            }, $result);
        }

        return $result;
    }
}