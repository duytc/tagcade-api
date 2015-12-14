<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\Query\ResultSetMapping;
use Tagcade\Domain\DTO\Report\UnifiedReport\AverageValue as AverageValueDTO;
use Tagcade\Entity\Report\UnifiedReport\PulsePoint\AccountManagement;
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

    /**
     * @inheritdoc
     */
    protected function getReportsInRange(\DateTime $startDate, \DateTime $endDate)
    {
        $qb = parent::getReportsInRange($startDate, $endDate);

        return $qb
            ->addOrderBy('r.adTagId', 'ASC')
            ->addOrderBy('r.date', 'ASC');
    }

    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @param int $defaultPageSize
     * @return array|bool
     */
    protected function getPaginationRecordsForAdTagGroupDay(PublisherInterface $publisher, UnifiedReportParams $params, $defaultPageSize = 10)
    {
        $searchField = $params->getSearchField();
        $searchKey = $params->getSearchKey();
        $sortField = $params->getSortField();
        $sortDirection = $params->getSortDirection();

        if ($params->getSize() == 0) {
            $params->setSize($defaultPageSize);
        }

        $offset = ($params->getPage() - 1) * $params->getSize();

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('publisherId', 'publisherId');
        $rsm->addScalarResult('size', 'size');
        $rsm->addScalarResult('adTagGroup', 'adTagGroup');
        $rsm->addScalarResult('totalImps', 'totalImps');
        $rsm->addScalarResult('paidImps', 'paidImps');
        $rsm->addScalarResult('fillRate', 'fillRate');
        $rsm->addScalarResult('adTag', 'adTag');
        $rsm->addScalarResult('adTagId', 'adTagId');
        $rsm->addScalarResult('askPrice', 'askPrice');
        $rsm->addScalarResult('revenue', 'revenue');
        $rsm->addScalarResult('backupImpression', 'backupImpression');
        $rsm->addScalarResult('avgCpm', 'avgCpm');
        $rsm->addScalarResult('status', 'status');
        $rsm->addScalarResult('date', 'date');

        $selectFromSearchQuery = "SELECT
                            id as id,
                            publisher_id as publisherId,
                            size as size,
                            ad_tag_group as adTagGroup,
                            total_imps as totalImps,
                            paid_imps as paidImps,
                            fill_rate as fillRate,
                            ad_tag as adTag,
                            ad_tag_id as adTagId,
                            ask_price as askPrice,
                            revenue as revenue,
                            backup_impression as backupImpression,
                            avg_cpm as avgCpm,
                            status as status,
                            date as date
                        FROM report_pulse_point_account_management
                        INNER JOIN ";
        $searchQuery = "SELECT
                          id
                      FROM report_pulse_point_account_management
                      WHERE
                          (date BETWEEN :start_date AND :end_date)
                          AND publisher_id = :publisher_id";
        $groupQuery = 'SELECT
                          r.id,
                          r.publisherId as publisherId,
                          r.size as size,
                          r.adTagGroup as adTagGroup,
                          r.adTag as adTag,
                          r.adTagId as adTagId,
                          r.status as status,
                          r.date as date,
                          (SUM(r.fillRate * r.paidImps) / SUM(r.paidImps)) as fillRate,
                          SUM(r.revenue) as revenue,
                          SUM(r.paidImps) as paidImps,
                          SUM(r.totalImps) as totalImps,
                          SUM(r.backupImpression) as backupImpression,
                          AVG(r.askPrice) as askPrice,
                          (SUM(r.avgCpm * r.revenue) / SUM(r.revenue)) as avgCpm
                       FROM (%s) as r
                       GROUP BY r.adTagGroup, r.date ';
        $finalQuery = 'SELECT * FROM (%s) as r1';

        $firstCondition = true;
        if (is_array($searchField) && $searchKey !== null) {
            $searchQuery .= " AND (";
            foreach ($searchField as $field) {
                switch ($field) {
                    case self::ACC_MNG_AD_TAG_GROUP :
                        $searchQuery .= $firstCondition ? ' ad_tag_group LIKE :ad_tag_group' : ' OR ad_tag_group LIKE :ad_tag_group';
                        $firstCondition = false;
                        break;
                    case self::ACC_MNG_AD_TAG_FIELD:
                        $searchQuery .= $firstCondition ? ' ad_tag LIKE :ad_tag' : ' OR ad_tag LIKE :ad_tag';
                        $firstCondition = false;
                        break;
                    case self::ACC_MNG_AD_TAG_ID_FIELD:
                        $searchQuery .= $firstCondition ? ' ad_tag_id = :ad_tag_id' : ' OR ad_tag_id = :ad_tag_id';
                        $firstCondition = false;
                        break;
                    case self::ACC_MNG_PUBLISHER_FIELD:
                        $searchQuery .= $firstCondition ? ' publisher_id = :publisher_id' : ' OR publisher_id = :publisher_id';
                        $firstCondition = false;
                        break;
                    case self::ACC_MNG_STATUS_FIELD:
                        $searchQuery .= $firstCondition ? ' status = :status' : ' OR status = :status';
                        $firstCondition = false;
                        break;
                }
            }

            $searchQuery .= ")";
        }

        $searchQuery = "(" . $searchQuery . ") as temp_tbl";
        $groupQuery = sprintf($groupQuery, $selectFromSearchQuery . $searchQuery . " using (id)");
        $finalQuery = sprintf($finalQuery, $groupQuery);

        // build sort query
        if ($sortField !== null && $sortDirection !== null &&
            in_array($sortDirection, [self::SORT_DIRECTION_ASC, self::SORT_DIRECTION_DESC]) &&
            in_array($sortField, [self::ACC_MNG_FILL_RATE_FIELD, self::ACC_MNG_TOTAL_IMPS_FIELD, self::ACC_MNG_PAID_IMPS_FIELD, self::ACC_MNG_REVENUE_FIELD, self::ACC_MNG_BACKUP_IMPRESSION_FIELD, self::ACC_MNG_DATE_FIELD, self::ACC_MNG_AVG_CPM_FIELD])
        ) {
            $finalQuery .= sprintf(" ORDER BY r1.%s %s ", $sortField, $sortDirection);
        } else {
            $finalQuery .= " ORDER BY r1.adTagGroup, r1.date asc ";
        }

        // build limit query
        $finalQuery .= "LIMIT :limit OFFSET :off_set ";

        // create final native query
        $query = $this->getEntityManager()->createNativeQuery($finalQuery, $rsm);

        if (is_array($searchField) && $searchKey !== null) {
            foreach ($searchField as $field) {
                switch ($field) {
                    case self::ACC_MNG_AD_TAG_GROUP :
                        $query->setParameter("ad_tag_group", '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::ACC_MNG_AD_TAG_FIELD:
                        $query->setParameter("ad_tag", '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::ACC_MNG_AD_TAG_ID_FIELD:
                        $query->setParameter("ad_tag_id", intval($searchKey), Type::INTEGER);
                        break;
                    case self::ACC_MNG_PUBLISHER_FIELD:
                        $query->setParameter("publisher_id", intval($searchKey), Type::INTEGER);
                        break;
                    case self::ACC_MNG_STATUS_FIELD:
                        $query->setParameter("status", $searchKey, Type::STRING);
                        break;
                }
            }
        }

        $query->setParameter('limit', $params->getSize(), Type::INTEGER);
        $query->setParameter('off_set', $offset, Type::INTEGER);
        $query->setParameter('start_date', $params->getStartDate(), Type::DATE);
        $query->setParameter('end_date', $params->getEndDate(), Type::DATE);
        $query->setParameter('publisher_id', $publisher->getId(), Type::INTEGER);

        $result = $query->getScalarResult();

        if (!is_array($result)) {
            return false;
        }

        return array_map(function ($rst) {
            return (is_array($rst) && count($rst) > 14)
                ? (new AccountManagement())
                    ->setId($rst['id'])
                    ->setPublisherId($rst['publisherId'])
                    ->setAdTagGroup($rst['adTagGroup'])
                    ->setAdTag($rst['adTag'])
                    ->setAdTagId($rst['adTagId'])
                    ->setAskPrice(is_numeric($rst['askPrice']) ? round($rst['askPrice'], 4) : 0)
                    ->setStatus($rst['status'])
                    ->setSize($rst['size'])
                    ->setFillRate(is_numeric($rst['fillRate']) ? round($rst['fillRate'], 4) : 0)
                    ->setPaidImps($rst['paidImps'])
                    ->setTotalImps($rst['totalImps'])
                    ->setRevenue(is_numeric($rst['revenue']) ? round($rst['revenue'], 4) : 0)
                    ->setBackupImpression($rst['backupImpression'])
                    ->setAvgCpm($rst['avgCpm'])
                    ->setDate((bool)strtotime($rst['date']) ? new \DateTime($rst['date']) : null)
                : null;
        }, $result);
    }

    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @return bool|AverageValueDTO
     */
    protected function getAverageValuesForAdTagGroupDay(PublisherInterface $publisher, UnifiedReportParams $params)
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('fillRate', 'fillRate');
        $rsm->addScalarResult('revenue', 'revenue');
        $rsm->addScalarResult('paidImps', 'paidImps');
        $rsm->addScalarResult('totalImps', 'totalImps');
        $rsm->addScalarResult('backupImpression', 'backupImpression');
        $rsm->addScalarResult('avgCpm', 'avgCpm');
        $rsm->addScalarResult('averageFillRate', 'averageFillRate');
        $rsm->addScalarResult('averageRevenue', 'averageRevenue');
        $rsm->addScalarResult('averagePaidImps', 'averagePaidImps');
        $rsm->addScalarResult('averageTotalImps', 'averageTotalImps');
        $rsm->addScalarResult('averageBackupImpression', 'averageBackupImpression');
        $rsm->addScalarResult('averageAvgCpm', 'averageAvgCpm');

        $mainQuery = 'SELECT
                          id,
                          SUM(revenue) as revenue,
                          SUM(paid_imps) as paidImps,
                          SUM(total_imps) as totalImps,
                          SUM(backup_impression) as backupImpression,
                          (SUM(fill_rate * paid_imps) / SUM(paid_imps)) as fillRate,
                          (SUM(avg_cpm * revenue) / SUM(revenue)) as avgCpm
                      FROM report_pulse_point_account_management
                      WHERE ( date BETWEEN :start_date AND :end_date) AND publisher_id = :publisher_id GROUP BY ad_tag_group, date';
        $select = 'SELECT
                      r.id,
                      (SUM(r.fillRate * r.paidImps) / SUM(r.paidImps)) as fillRate,
                      AVG(r.fillRate) as averageFillRate,
                      SUM(r.revenue) as revenue,
                      AVG(r.revenue) as averageRevenue,
                      SUM(r.paidImps) as paidImps,
                      AVG(r.paidImps) as averagePaidImps,
                      SUM(r.totalImps) as totalImps,
                      AVG(r.totalImps) as averageTotalImps,
                      SUM(r.backupImpression) as backupImpression,
                      AVG(r.backupImpression) as averageBackupImpression,
                      (SUM(r.avgCpm * r.revenue) / SUM(r.revenue)) as avgCpm,
                      AVG(r.avgCpm) as averageAvgCpm
                   FROM (%s) as r';
        $nativeQuery = $this->getEntityManager()->createNativeQuery(sprintf($select, $mainQuery), $rsm);
        $nativeQuery->setParameter('start_date', $params->getStartDate(), Type::DATE);
        $nativeQuery->setParameter('end_date', $params->getEndDate(), Type::DATE);
        $nativeQuery->setParameter('publisher_id', $publisher->getId(), Type::INTEGER);

        $rst = current($nativeQuery->getScalarResult());

        if (!is_array($rst) || count($rst) < 12) {
            return false;
        }

        return (new AverageValueDTO())
            ->setFillRate(is_numeric($rst['fillRate']) ? round($rst['fillRate'], 4) : null)
            ->setPaidImps($rst['paidImps'])
            ->setTotalImps($rst['totalImps'])
            ->setRevenue(is_numeric($rst['revenue']) ? round($rst['revenue'], 4) : null)
            ->setBackupImpression($rst['backupImpression'])
            ->setAvgCpm($rst['avgCpm'])
            ->setAverageFillRate(is_numeric($rst['averageFillRate']) ? round($rst['averageFillRate'], 4) : null)
            ->setAveragePaidImps(is_numeric($rst['averagePaidImps']) ? round($rst['averagePaidImps'], 0) : null)
            ->setAverageTotalImps(is_numeric($rst['averageTotalImps']) ? round($rst['averageTotalImps'], 0) : null)
            ->setAverageRevenue(is_numeric($rst['averageRevenue']) ? round($rst['averageRevenue'], 4) : null)
            ->setAverageBackupImpression(is_numeric($rst['averageBackupImpression']) ? round($rst['averageBackupImpression'], 0) : null)
            ->setAverageAvgCpm(is_numeric($rst['averageAvgCpm']) ? round($rst['averageAvgCpm'], 4) : null);
    }

    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @return int
     */
    protected function getTotalRecordsForAdTagGroupDay(PublisherInterface $publisher, UnifiedReportParams $params)
    {
        $searchField = $params->getSearchField();
        $searchKey = $params->getSearchKey();

        $qb = $this->createQueryBuilder('r')->select('count(r.id) as total');

        $qb
            ->andWhere($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->andWhere('r.publisherId = :publisherId')
            ->setParameter('start_date', $params->getStartDate(), Type::DATE)
            ->setParameter('end_date', $params->getEndDate(), Type::DATE)
            ->setParameter('publisherId', $publisher->getId());

        $nestedQuery = '';

        if (is_array($searchField) && $searchKey !== null) {
            foreach ($searchField as $field) {
                switch ($field) {
                    case self::ACC_MNG_AD_TAG_GROUP :
                        $nestedQuery .= empty($nestedQuery) ? ' r.adTagGroup LIKE :ad_tag_group' : ' OR r.adTagGroup LIKE :ad_tag_group';
                        break;
                    case self::ACC_MNG_AD_TAG_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.adTag LIKE :ad_tag' : ' OR r.adTag LIKE :ad_tag';
                        break;
                    case self::ACC_MNG_AD_TAG_ID_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.adTagId = :ad_tag_id' : ' OR r.adTagId = :ad_tag_id';
                        break;
                    case self::ACC_MNG_PUBLISHER_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.publisherId = :publisher_id' : ' OR r.publisherId = :publisher_id';
                        break;
                    case self::ACC_MNG_STATUS_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.status = :status' : ' OR r.status = :status';
                        break;
                }
            }

            $qb->andWhere($nestedQuery);

            foreach ($searchField as $field) {
                switch ($field) {
                    case self::ACC_MNG_AD_TAG_GROUP :
                        $qb->setParameter('ad_tag_group', '%' . $searchKey . '%');
                        break;
                    case self::ACC_MNG_AD_TAG_FIELD:
                        $qb->setParameter('ad_tag', '%' . $searchKey . '%');
                        break;
                    case self::ACC_MNG_AD_TAG_ID_FIELD:
                        $qb->setParameter('ad_tag_id', $searchKey, Type::STRING);
                        break;
                    case self::ACC_MNG_PUBLISHER_FIELD:
                        $qb->setParameter('publisher_id', intval($searchKey), Type::INTEGER);
                        break;
                    case self::ACC_MNG_STATUS_FIELD:
                        $qb->setParameter('status', $searchKey, Type::STRING);
                        break;
                }
            }
        }

        $qb->addGroupBy('r.adTagGroup, r.date');

        return count($qb->getQuery()->getScalarResult());
    }


    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @return mixed
     */
    protected function getAverageValues(PublisherInterface $publisher, UnifiedReportParams $params)
    {
        $qb = parent::getReportsInRange($params->getStartDate(), $params->getEndDate());

        $result = $qb
            ->addSelect('(SUM(r.fillRate * r.paidImps) / SUM(r.paidImps)) as fillRate')
            ->addSelect('AVG(r.fillRate) as averageFillRate')
            ->addSelect('SUM(r.paidImps) as paidImps')
            ->addSelect('AVG(r.paidImps) as averagePaidImps')
            ->addSelect('SUM(r.totalImps) as totalImps')
            ->addSelect('AVG(r.totalImps) as averageTotalImps')
            ->addSelect('SUM(r.revenue) as revenue')
            ->addSelect('AVG(r.revenue) as averageRevenue')
            ->addSelect('SUM(r.backupImpression) as backupImpression')
            ->addSelect('AVG(r.backupImpression) as averageBackupImpression')
            ->addSelect('(SUM(r.avgCpm * r.revenue) / SUM(r.revenue)) as avgCpm')
            ->addSelect('AVG(r.avgCpm) as averageAvgCpm')
            ->andWhere('r.publisherId = :publisherId')
            ->setParameter('publisherId', $publisher->getId())
            ->getQuery()
            ->getResult();

        if (is_array($result)) {
            $result = array_map(function ($rst) {
                return (is_array($rst) && count($rst) > 12)
                    ? (new AverageValueDTO())
                        ->setFillRate(is_numeric($rst['fillRate']) ? round($rst['fillRate'], 4) : null)
                        ->setPaidImps($rst['paidImps'])
                        ->setTotalImps($rst['totalImps'])
                        ->setRevenue(is_numeric($rst['revenue']) ? round($rst['revenue'], 4) : null)
                        ->setBackupImpression($rst['backupImpression'])
                        ->setAvgCpm($rst['avgCpm'])
                        ->setAverageFillRate(is_numeric($rst['averageFillRate']) ? round($rst['averageFillRate'], 4) : null)
                        ->setAveragePaidImps(is_numeric($rst['averagePaidImps']) ? round($rst['averagePaidImps'], 0) : null)
                        ->setAverageTotalImps(is_numeric($rst['averageTotalImps']) ? round($rst['averageTotalImps'], 0) : null)
                        ->setAverageRevenue(is_numeric($rst['averageRevenue']) ? round($rst['averageRevenue'], 4) : null)
                        ->setAverageBackupImpression(is_numeric($rst['averageBackupImpression']) ? round($rst['averageBackupImpression'], 0) : null)
                        ->setAverageAvgCpm(is_numeric($rst['averageAvgCpm']) ? round($rst['averageAvgCpm'], 4) : null)
                    : null;
            }, $result);
        }

        return current($result);
    }

    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @return mixed
     */
    protected function getTotalRecords(PublisherInterface $publisher, UnifiedReportParams $params)
    {
        $searchField = $params->getSearchField();
        $searchKey = $params->getSearchKey();

        $qb = $this->createQueryBuilder('r')->select('count(r.id) as total');

        $qb
            ->andWhere($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->andWhere('r.publisherId = :publisherId')
            ->setParameter('start_date', $params->getStartDate(), Type::DATE)
            ->setParameter('end_date', $params->getEndDate(), Type::DATE)
            ->setParameter('publisherId', $publisher->getId());

        $nestedQuery = '';

        if (is_array($searchField) && $searchKey !== null) {
            foreach ($searchField as $field) {
                switch ($field) {
                    case self::ACC_MNG_AD_TAG_GROUP :
                        $nestedQuery .= empty($nestedQuery) ? ' r.adTagGroup LIKE :ad_tag_group' : ' OR r.adTagGroup LIKE :ad_tag_group';
                        break;
                    case self::ACC_MNG_AD_TAG_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.adTag LIKE :ad_tag' : ' OR r.adTag LIKE :ad_tag';
                        break;
                    case self::ACC_MNG_AD_TAG_ID_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.adTagId = :ad_tag_id' : ' OR r.adTagId = :ad_tag_id';
                        break;
                    case self::ACC_MNG_PUBLISHER_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.publisherId = :publisher_id' : ' OR r.publisherId = :publisher_id';
                        break;
                    case self::ACC_MNG_STATUS_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.status = :status' : ' OR r.status = :status';
                        break;
                }
            }

            $qb->andWhere($nestedQuery);

            foreach ($searchField as $field) {
                switch ($field) {
                    case self::ACC_MNG_AD_TAG_GROUP :
                        $qb->setParameter('ad_tag_group', '%' . $searchKey . '%');
                        break;
                    case self::ACC_MNG_AD_TAG_FIELD:
                        $qb->setParameter('ad_tag', '%' . $searchKey . '%');
                        break;
                    case self::ACC_MNG_AD_TAG_ID_FIELD:
                        $qb->setParameter('ad_tag_id', $searchKey, Type::STRING);
                        break;
                    case self::ACC_MNG_PUBLISHER_FIELD:
                        $qb->setParameter('publisher_id', intval($searchKey), Type::INTEGER);
                        break;
                    case self::ACC_MNG_STATUS_FIELD:
                        $qb->setParameter('status', $searchKey, Type::STRING);
                        break;
                }
            }
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @param int $defaultPageSize
     * @return array
     */
    protected function getPaginationRecords(PublisherInterface $publisher, UnifiedReportParams $params, $defaultPageSize = 10)
    {
        $searchField = $params->getSearchField();
        $searchKey = $params->getSearchKey();
        $sortField = $params->getSortField();
        $sortDirection = $params->getSortDirection();

        if ($params->getSize() == 0) {
            $params->setSize($defaultPageSize);
        }

        $offset = ($params->getPage() - 1) * $params->getSize();

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(AccountManagement::class, 'a');
        $rsm->addFieldResult('a', 'id', 'id');
        $rsm->addFieldResult('a', 'ad_tag_group', 'adTagGroup');
        $rsm->addFieldResult('a', 'publisher_id', 'publisherId');
        $rsm->addFieldResult('a', 'ad_tag', 'adTag');
        $rsm->addFieldResult('a', 'ad_tag_id', 'adTagId');
        $rsm->addFieldResult('a', 'ask_price', 'askPrice');
        $rsm->addFieldResult('a', 'fill_rate', 'fillRate');
        $rsm->addFieldResult('a', 'revenue', 'revenue');
        $rsm->addFieldResult('a', 'paid_imps', 'paidImps');
        $rsm->addFieldResult('a', 'total_imps', 'totalImps');
        $rsm->addFieldResult('a', 'backup_impression', 'backupImpression');
        $rsm->addFieldResult('a', 'avg_cpm', 'avgCpm');
        $rsm->addFieldResult('a', 'date', 'date');
        $rsm->addFieldResult('a', 'status', 'status');
        $rsm->addFieldResult('a', 'size', 'size');

        $selectQuery = "SELECT id, size, ad_tag_group, total_imps, paid_imps, fill_rate, ad_tag, ad_tag_id, ask_price, revenue, backup_impression, avg_cpm, date FROM report_pulse_point_account_management INNER JOIN ";
        $mainQuery = "SELECT id FROM report_pulse_point_account_management WHERE (date BETWEEN :start_date AND :end_date) AND publisher_id = :publisher_id";

        $firstCondition = true;
        if (is_array($searchField) && $searchKey !== null) {
            $mainQuery .= " AND (";
            foreach ($searchField as $field) {
                switch ($field) {
                    case self::ACC_MNG_AD_TAG_GROUP :
                        $mainQuery .= $firstCondition ? ' ad_tag_group LIKE :ad_tag_group' : ' OR ad_tag_group LIKE :ad_tag_group';
                        $firstCondition = false;
                        break;
                    case self::ACC_MNG_AD_TAG_FIELD:
                        $mainQuery .= $firstCondition ? ' ad_tag LIKE :ad_tag' : ' OR ad_tag LIKE :ad_tag';
                        $firstCondition = false;
                        break;
                    case self::ACC_MNG_AD_TAG_ID_FIELD:
                        $mainQuery .= $firstCondition ? ' ad_tag_id = :ad_tag_id' : ' OR ad_tag_id = :ad_tag_id';
                        $firstCondition = false;
                        break;
                    case self::ACC_MNG_PUBLISHER_FIELD:
                        $mainQuery .= $firstCondition ? ' publisher_id = :publisher_id' : ' OR publisher_id = :publisher_id';
                        $firstCondition = false;
                        break;
                    case self::ACC_MNG_STATUS_FIELD:
                        $mainQuery .= $firstCondition ? ' status = :status' : ' OR status = :status';
                        $firstCondition = false;
                        break;
                }
            }

            $mainQuery .= ")";
        }

        if ($sortField !== null && $sortDirection !== null &&
            in_array($sortDirection, [self::SORT_DIRECTION_ASC, self::SORT_DIRECTION_DESC]) &&
            in_array($sortField, [self::ACC_MNG_FILL_RATE_FIELD, self::ACC_MNG_TOTAL_IMPS_FIELD, self::ACC_MNG_PAID_IMPS_FIELD, self::ACC_MNG_REVENUE_FIELD, self::ACC_MNG_BACKUP_IMPRESSION_FIELD, self::ACC_MNG_DATE_FIELD, self::ACC_MNG_AVG_CPM_FIELD])
        ) {
            $mainQuery .= $this->appendOrderBy($sortField, $sortDirection);
        } else {
            $mainQuery .= ' ORDER BY id asc';
        }

        $mainQuery .= " LIMIT :limit OFFSET :off_set";
        $mainQuery = "(" . $mainQuery . ") as temp_tbl";
        $query = $this->getEntityManager()->createNativeQuery($selectQuery . $mainQuery . " using (id)", $rsm);

        if (is_array($searchField) && $searchKey !== null) {
            foreach ($searchField as $field) {
                switch ($field) {
                    case self::ACC_MNG_AD_TAG_GROUP :
                        $query->setParameter("ad_tag_group", '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::ACC_MNG_AD_TAG_FIELD:
                        $query->setParameter("ad_tag", '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::ACC_MNG_AD_TAG_ID_FIELD:
                        $query->setParameter("ad_tag_id", intval($searchKey), Type::INTEGER);
                        break;
                    case self::ACC_MNG_PUBLISHER_FIELD:
                        $query->setParameter("publisher_id", intval($searchKey), Type::INTEGER);
                        break;
                    case self::ACC_MNG_STATUS_FIELD:
                        $query->setParameter("status", $searchKey, Type::STRING);
                        break;
                }
            }
        }

        $query->setParameter('limit', $params->getSize(), Type::INTEGER);
        $query->setParameter('off_set', $offset, Type::INTEGER);
        $query->setParameter('start_date', $params->getStartDate(), Type::DATE);
        $query->setParameter('end_date', $params->getEndDate(), Type::DATE);
        $query->setParameter('publisher_id', $publisher->getId());

        return $query->getResult();
    }

    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @param int $defaultPageSize
     * @return array
     */
    public function getReportsForAdTagGroupDay(PublisherInterface $publisher, UnifiedReportParams $params, $defaultPageSize = 10)
    {
        return array(
            self::REPORT_AVERAGE_VALUES => $this->getAverageValuesForAdTagGroupDay($publisher, $params),
            self::REPORT_PAGINATION_RECORDS => $this->getPaginationRecordsForAdTagGroupDay($publisher, $params, $defaultPageSize),
            self::REPORT_TOTAL_RECORDS => $this->getTotalRecordsForAdTagGroupDay($publisher, $params)
        );
    }

    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @param int $defaultPageSize
     * @return array
     */
    public function getReports(PublisherInterface $publisher, UnifiedReportParams $params, $defaultPageSize = 10)
    {
        return array(
            self::REPORT_AVERAGE_VALUES => $this->getAverageValues($publisher, $params),
            self::REPORT_PAGINATION_RECORDS => $this->getPaginationRecords($publisher, $params, $defaultPageSize),
            self::REPORT_TOTAL_RECORDS => $this->getTotalRecords($publisher, $params)
        );
    }
}