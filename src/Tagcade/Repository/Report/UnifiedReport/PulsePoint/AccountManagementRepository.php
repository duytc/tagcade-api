<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;


use Doctrine\ORM\Mapping;
use Tagcade\Domain\DTO\Report\UnifiedReport\AdTagGroupDaily as AdTagGroupDailyDTO;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class AccountManagementRepository extends AbstractReportRepository implements AccountManagementRepositoryInterface
{
    // search fields
    const ACC_MNG_PUBLISHER_FIELD = "publisherId";
    const ACC_MNG_DOMAIN_FIELD = "domain";
    const ACC_MNG_AD_TAG_ID_FIELD = "adTagId";
    const ACC_MNG_AD_TAG_FIELD = "adTag";
    const ACC_MNG_DOMAIN_STATUS_FIELD = "domainStatus";
    // sort fields
    const ACC_MNG_FILL_RATE_FIELD = "fillRate";
    const ACC_MNG_PAID_IMPS_FIELD = "paidImps";
    const ACC_MNG_TOTAL_IMPS_FIELD = "totalImps";
    const ACC_MNG_DATE_FIELD = "date";
    // sort direction
    // sort direction
    const SORT_DIRECTION_ASC = "asc";
    const SORT_DIRECTION_DESC = "desc";

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
                    ? (new AdTagGroupDailyDTO())
                        ->setId($rst['id'])
                        ->setPublisherId($rst['publisherId'])
                        ->setAdTagGroup($rst['adTagGroup'])
                        ->setRevenue(is_numeric($rst['revenue']) ? round($rst['revenue'], 4) : null)
                        ->setFillRate(is_numeric($rst['fillRate']) ? round($rst['fillRate'], 4) : null)
                        ->setPaidImps($rst['paidImps'])
                        ->setBackupImpression($rst['backupImpression'])
                        ->setTotalImps($rst['totalImps'])
                        ->setAvgCpm(is_numeric($rst['avgCpm']) ? round($rst['avgCpm'], 4) : null)
                        ->setDate($rst['date'])
                    : null;
            }, $result);
        }

        return $result;
    }

    /**
     * @param UnifiedReportParams $params
     * @return mixed
     */
    protected function getQueryForPaginator(UnifiedReportParams $params)
    {
        // TODO: Implement getQueryForPaginator() method.
    }
}