<?php

namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;
use Tagcade\Domain\DTO\Report\UnifiedReport\AverageValue as AverageValueDTO;
use Tagcade\Entity\Report\UnifiedReport\PulsePoint\Daily as DailyEntity;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class DailyReportRepository extends AbstractReportRepository implements DailyReportRepositoryInterface
{
    // search fields
    const DAILY_REPORT_PUBLISHER_FIELD = "publisherId";
    const DAILY_REPORT_SIZE_FIELD = "size";
    // sort fields
    const DAILY_REPORT_FILL_RATE_FIELD = "fillRate";
    const DAILY_REPORT_PAID_IMPS_FIELD = "paidImps";
    const DAILY_REPORT_TOTAL_IMPS_FIELD = "totalImps";
    const DAILY_REPORT_REVENUE_FIELD = "revenue";
    const DAILY_REPORT_BACKUP_IMPRESSION_FIELD = "backupImpression";
    const DAILY_REPORT_AVG_CPM_FIELD = "avgCpm";
    const DAILY_REPORT_DATE_FIELD = "date";
    // sort direction
    // sort direction
    const SORT_DIRECTION_ASC = "asc";
    const SORT_DIRECTION_DESC = "desc";

    protected function getReportsInRange(\DateTime $startDate, \DateTime $endDate)
    {
        $qb = parent::getReportsInRange($startDate, $endDate);

        return $qb
            ->addOrderBy('r.date', 'ASC');
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
                        ->setAvgCpm(is_numeric($rst['avgCpm']) ? round($rst['avgCpm'], 4) : null)
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
                    case self::DAILY_REPORT_SIZE_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.size LIKE :size' : ' OR r.size LIKE :size';
                        break;
                    case self::DAILY_REPORT_PUBLISHER_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.publisherId = :publisher_id' : ' OR r.publisherId = :publisher_id';
                        break;
                }
            }

            $qb->andWhere($nestedQuery);

            foreach ($searchField as $field) {
                switch ($field) {
                    case self::DAILY_REPORT_SIZE_FIELD:
                        $qb->setParameter('size', '%' . $searchKey . '%');
                        break;
                    case self::DAILY_REPORT_PUBLISHER_FIELD:
                        $qb->setParameter('publisher_id', intval($searchKey), Type::INTEGER);
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
        $rsm->addEntityResult(DailyEntity::class, 'a');
        $rsm->addFieldResult('a', 'id', 'id');
        $rsm->addFieldResult('a', 'publisher_id', 'publisherId');
        $rsm->addFieldResult('a', 'date', 'date');
        $rsm->addFieldResult('a', 'size', 'size');
        $rsm->addFieldResult('a', 'revenue', 'revenue');
        $rsm->addFieldResult('a', 'fill_rate', 'fillRate');
        $rsm->addFieldResult('a', 'paid_imps', 'paidImps');
        $rsm->addFieldResult('a', 'backup_impression', 'backupImpression');
        $rsm->addFieldResult('a', 'total_imps', 'totalImps');
        $rsm->addFieldResult('a', 'avg_cpm', 'avgCpm');

        $selectQuery = "SELECT id, date, size, revenue, fill_rate, paid_imps, backup_impression, total_imps, avg_cpm FROM report_pulse_point_daily INNER JOIN ";
        $mainQuery = "SELECT id FROM report_pulse_point_daily WHERE (date BETWEEN :start_date AND :end_date) AND publisher_id = :publisher_id";

        $firstCondition = true;
        if (is_array($searchField) && $searchKey !== null) {
            $mainQuery = $mainQuery . " AND (";
            foreach ($searchField as $field) {
                switch ($field) {
                    case self::DAILY_REPORT_SIZE_FIELD :
                        $mainQuery .= $firstCondition ? ' size LIKE :size' : ' OR size LIKE :size';
                        $firstCondition = false;
                        break;
                    case self::DAILY_REPORT_PUBLISHER_FIELD:
                        $mainQuery .= $firstCondition ? ' publisher_id = :publisher_id' : ' OR publisher_id = :publisher_id';
                        $firstCondition = false;
                        break;
                }
            }

            $mainQuery .= ")";
        }

        if ($sortField !== null && $sortDirection !== null &&
            in_array($sortDirection, [self::SORT_DIRECTION_ASC, self::SORT_DIRECTION_DESC]) &&
            in_array($sortField, [self::DAILY_REPORT_AVG_CPM_FIELD, self::DAILY_REPORT_TOTAL_IMPS_FIELD, self::DAILY_REPORT_BACKUP_IMPRESSION_FIELD, self::DAILY_REPORT_DATE_FIELD, self::DAILY_REPORT_SIZE_FIELD, self::DAILY_REPORT_REVENUE_FIELD, self::DAILY_REPORT_FILL_RATE_FIELD, self::DAILY_REPORT_PAID_IMPS_FIELD])
        ) {
            $mainQuery .= $this->appendOrderBy($sortField, $sortDirection);
        } else {
            $mainQuery .= " order by id asc";
        }

        $mainQuery .= " LIMIT :limit OFFSET :off_set";
        $mainQuery = "(" . $mainQuery . ") as temp_tbl";
        $query = $this->getEntityManager()->createNativeQuery($selectQuery . $mainQuery . " using (id)", $rsm);

        if (is_array($searchField) && $searchKey !== null) {
            foreach ($searchField as $field) {
                switch ($field) {
                    case self::DAILY_REPORT_SIZE_FIELD :
                        $query->setParameter("size", '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::DAILY_REPORT_PUBLISHER_FIELD :
                        $query->setParameter("publisher_id", intval($searchKey), Type::INTEGER);
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
    public function getReports(PublisherInterface $publisher, UnifiedReportParams $params, $defaultPageSize = 10)
    {
        return array(
            self::REPORT_AVERAGE_VALUES => $this->getAverageValues($publisher, $params),
            self::REPORT_PAGINATION_RECORDS => $this->getPaginationRecords($publisher, $params, $defaultPageSize),
            self::REPORT_TOTAL_RECORDS => $this->getTotalRecords($publisher, $params)
        );
    }
}