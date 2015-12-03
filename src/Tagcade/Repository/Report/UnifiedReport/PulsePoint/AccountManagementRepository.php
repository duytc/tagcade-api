<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;


use Tagcade\Model\Report\UnifiedReport\PulsePoint\AccountManagement;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;

class AccountManagementRepository extends AbstractReportRepository implements AccountManagementRepositoryInterface
{
    protected function getReportsInRange(\DateTime $startDate, \DateTime $endDate)
    {
        $qb = parent::getReportsInRange($startDate, $endDate);

        return $qb
            ->addOrderBy('r.adTagId', 'ASC')
            ->addOrderBy('r.date', 'ASC');
    }

    public function getAdTagGroupDailyReportFor(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate)
    {
        $qb = parent::getReportsInRange($startDate, $endDate);

        $result = $qb
            ->andWhere('r.publisherId = :publisherId')
            ->setParameter('publisherId', $publisher->getId())
            ->addSelect('r.id')
            ->addSelect('r.publisherId')
            ->addSelect('r.adTagGroup')
            ->addSelect('r.adTag')
            ->addSelect('r.adTagId')
            ->addSelect('r.status')
            ->addSelect('r.size')
            ->addSelect('r.askPrice')
            ->addSelect('SUM(r.revenue) as revenue')
            ->addSelect('(SUM(r.fillRate * r.paidImps) / SUM(r.paidImps)) as fillRate')
            ->addSelect('SUM(r.paidImps) as paidImps')
            ->addSelect('SUM(r.backupImpression) as backupImpression')
            ->addSelect('SUM(r.totalImps) as totalImps')
            ->addSelect('(SUM(r.avgCpm * r.revenue) / SUM(r.revenue)) as avgCpm')
            ->addSelect('r.date')
            ->addGroupBy('r.adTagGroup, r.date')
            ->getQuery()
            ->getResult();

        // TODO: get result as array of DailyCountry objects, not mixed array ([Original AccountManagement, id, publisherId, ...]
        if (is_array($result)) {
            $result = array_map(function ($rst) {
                return (is_array($rst) && count($rst) > 10)
                    ? (new AccountManagement())
                        ->setId($rst['id'])
                        ->setPublisherId($rst['publisherId'])
                        ->setAdTagGroup($rst['adTagGroup'])
                        ->setAdTag($rst['adTag'])
                        ->setAdTagId($rst['adTagId'])
                        ->setStatus($rst['status'])
                        ->setSize($rst['size'])
                        ->setAskPrice($rst['askPrice'])
                        ->setRevenue($rst['revenue'])
                        ->setFillRate($rst['fillRate'])
                        ->setPaidImps($rst['paidImps'])
                        ->setBackupImpression($rst['backupImpression'])
                        ->setTotalImps($rst['totalImps'])
                        ->setAvgCpm($rst['avgCpm'])
                        ->setDate($rst['date'])
                    : null;
            }, $result);
        }

        return $result;
    }
}