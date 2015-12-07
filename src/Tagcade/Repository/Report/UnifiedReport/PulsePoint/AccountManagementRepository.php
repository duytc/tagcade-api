<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping;
use Tagcade\Domain\DTO\Report\UnifiedReport\AdTagGroupDaily as AdTagGroupDailyDTO;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class AccountManagementRepository extends AbstractReportRepository implements AccountManagementRepositoryInterface
{
    // search fields
    const ACC_MNG_PUBLISHER_FIELD = "publisherId";
    const ACC_MNG_AD_TAG_GROUP = "adTagGroup";
    const ACC_MNG_AD_TAG_ID_FIELD = "adTagId";
    const ACC_MNG_AD_TAG_FIELD = "adTag";
    const ACC_MNG_STATUS_FIELD = "status";
    // sort fields
    const ACC_MNG_FILL_RATE_FIELD = "fillRate";
    const ACC_MNG_PAID_IMPS_FIELD = "paidImps";
    const ACC_MNG_TOTAL_IMPS_FIELD = "totalImps";
    const ACC_MNG_REVENUE_FIELD = "revenue";
    const ACC_MNG_BACKUP_IMPRESSION_FIELD = "backupImpression";
    const ACC_MNG_AVG_CPM_FIELD = "avCpm";
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
    public function getQueryForPaginator(UnifiedReportParams $params)
    {
        $searchField = $params->getSearchField();
        $searchKey = $params->getSearchKey();
        $sortField = $params->getSortField();
        $sortDirection = $params->getSortDirection();

        $qb = $this->createQueryBuilder('r');

        $qb
            ->andWhere($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $params->getStartDate(), Type::DATE)
            ->setParameter('end_date', $params->getEndDate(), Type::DATE)
        ;

        if (is_array($searchField) && $searchKey !== null) {
            foreach($searchField as $field) {
                switch ($field) {
                    case self::ACC_MNG_AD_TAG_GROUP :
                        $qb->andWhere('r.adTagGroup LIKE :ad_tag_group')->setParameter('ad_tag_group', '%'.$searchKey.'%');
                        break;
                    case self::ACC_MNG_AD_TAG_FIELD:
                        $qb->andWhere('r.adTag LIKE :ad_tag')->setParameter('ad_tag', '%'.$searchKey.'%');
                        break;
                    case self::ACC_MNG_AD_TAG_ID_FIELD:
                        $qb->andWhere('r.adTagId = :ad_tag_id')->setParameter('ad_tag_id', $searchKey, Type::STRING);
                        break;
                    case self::ACC_MNG_PUBLISHER_FIELD:
                        $qb->andWhere('r.publisherId = :publisher_id')
                            ->setParameter('publisher_id', intval($searchKey), Type::INTEGER);
                        break;
                    case self::ACC_MNG_STATUS_FIELD:
                        $qb->andWhere('r.status = :status')
                            ->setParameter('status', $searchKey, Type::STRING);
                        break;
                }
            }
        }

        if ($sortField !== null && $sortDirection !== null && in_array($sortDirection, [self::SORT_DIRECTION_ASC, self::SORT_DIRECTION_DESC])) {
            switch ($sortField) {
                case self::ACC_MNG_FILL_RATE_FIELD:
                    $qb->addOrderBy('r.fillRate', $sortDirection);
                    break;
                case self::ACC_MNG_PAID_IMPS_FIELD:
                    $qb->addOrderBy('r.paidImps', $sortDirection);
                    break;
                case self::ACC_MNG_TOTAL_IMPS_FIELD:
                    $qb->addOrderBy('r.totalImps', $sortDirection);
                    break;
                case self::ACC_MNG_AVG_CPM_FIELD:
                    $qb->addOrderBy('r.avgCpm', $sortDirection);
                    break;
                case self::ACC_MNG_BACKUP_IMPRESSION_FIELD:
                    $qb->addOrderBy('r.backupImpression', $sortDirection);
                    break;
                case self::ACC_MNG_REVENUE_FIELD:
                    $qb->addOrderBy('r.revenue', $sortDirection);
                    break;
                case self::ACC_MNG_DATE_FIELD:
                    $qb->addOrderBy('r.date', $sortDirection);
                    break;
            }
        }
        else {
            $qb->addOrderBy('r.id', 'asc');
        }

        return $qb->getQuery();
    }
}