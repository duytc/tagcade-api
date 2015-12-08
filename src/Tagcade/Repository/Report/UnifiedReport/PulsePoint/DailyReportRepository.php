<?php

namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;

use Doctrine\DBAL\Types\Type;
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

        $nestedQuery = '';

        if (is_array($searchField) && $searchKey !== null) {
            foreach($searchField as $field) {
                switch ($field) {
                    case self::DAILY_REPORT_SIZE_FIELD:
                        $query = empty($nestedQuery) ? ' r.size LIKE :size' : ' OR r.size LIKE :size';
                        $nestedQuery = $nestedQuery . $query;
                        break;
                    case self::DAILY_REPORT_PUBLISHER_FIELD:
                        $query = empty($nestedQuery) ? ' r.publisherId = :publisher_id' : ' OR r.publisherId = :publisher_id';
                        $nestedQuery = $nestedQuery . $query;
                        break;
                }
            }

            $qb->andWhere($nestedQuery);

            foreach($searchField as $field) {
                switch ($field) {
                    case self::DAILY_REPORT_SIZE_FIELD:
                        $qb->setParameter('size', '%'.$searchKey.'%');
                        break;
                    case self::DAILY_REPORT_PUBLISHER_FIELD:
                        $qb->setParameter('publisher_id', intval($searchKey), Type::INTEGER);
                        break;
                }
            }
        }

        if ($sortField !== null && $sortDirection !== null && in_array($sortDirection, [self::SORT_DIRECTION_ASC, self::SORT_DIRECTION_DESC])) {
            switch ($sortField) {
                case self::DAILY_REPORT_FILL_RATE_FIELD:
                    $qb->addOrderBy('r.fillRate', $sortDirection);
                    break;
                case self::DAILY_REPORT_PAID_IMPS_FIELD:
                    $qb->addOrderBy('r.paidImps', $sortDirection);
                    break;
                case self::DAILY_REPORT_TOTAL_IMPS_FIELD:
                    $qb->addOrderBy('r.totalImps', $sortDirection);
                    break;
                case self::DAILY_REPORT_BACKUP_IMPRESSION_FIELD:
                    $qb->addOrderBy('r.backupImpression', $sortDirection);
                    break;
                case self::DAILY_REPORT_AVG_CPM_FIELD:
                    $qb->addOrderBy('r.avgCpm', $sortDirection);
                    break;
                case self::DAILY_REPORT_REVENUE_FIELD:
                    $qb->addOrderBy('r.revenue', $sortDirection);
                    break;
                case self::DAILY_REPORT_DATE_FIELD:
                    $qb->addOrderBy('r.date', $sortDirection);
                    break;
            }
        }
        else {
            $qb->addOrderBy('r.date', 'asc');
        }

        return $qb->getQuery();
    }
}