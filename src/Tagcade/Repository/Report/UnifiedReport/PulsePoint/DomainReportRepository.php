<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;


use Doctrine\DBAL\Types\Type;
use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class DomainReportRepository extends AbstractReportRepository implements DomainReportRepositoryInterface
{
    // search fields
    const DOMAIN_REPORT_PUBLISHER_FIELD = "publisherId";
    const DOMAIN_REPORT_DOMAIN_FIELD = "domain";
    const DOMAIN_REPORT_DOMAIN_STATUS_FIELD = "domainStatus";
    // sort fields
    const DOMAIN_REPORT_FILL_RATE_FIELD = "fillRate";
    const DOMAIN_REPORT_PAID_IMPS_FIELD = "paidImps";
    const DOMAIN_REPORT_TOTAL_IMPS_FIELD = "totalImps";
    const DOMAIN_REPORT_DATE_FIELD = "date";
    // sort direction
    // sort direction
    const SORT_DIRECTION_ASC = "asc";
    const SORT_DIRECTION_DESC = "desc";
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
                    case self::DOMAIN_REPORT_DOMAIN_FIELD:
                        $qb->andWhere('r.domain LIKE :domain')->setParameter('domain', '%'.$searchKey.'%');
                        break;
                    case self::DOMAIN_REPORT_DOMAIN_STATUS_FIELD:
                        $qb->andWhere('r.domainStatus = :domain_status')->setParameter('domain_status', $searchKey);
                        break;
                    case self::DOMAIN_REPORT_PUBLISHER_FIELD:
                        $qb->andWhere('r.publisherId = :publisher_id')
                            ->setParameter('publisher_id', intval($searchKey), Type::INTEGER);
                        break;
                }
            }
        }

        if ($sortField !== null && $sortDirection !== null && in_array($sortDirection, [self::SORT_DIRECTION_ASC, self::SORT_DIRECTION_DESC])) {
            switch ($sortField) {
                case self::DOMAIN_REPORT_FILL_RATE_FIELD:
                    $qb->addOrderBy('r.fillRate', $sortDirection);
                    break;
                case self::DOMAIN_REPORT_PAID_IMPS_FIELD:
                    $qb->addOrderBy('r.paidImps', $sortDirection);
                    break;
                case self::DOMAIN_REPORT_TOTAL_IMPS_FIELD:
                    $qb->addOrderBy('r.totalImps', $sortDirection);
                    break;
                case self::DOMAIN_REPORT_DATE_FIELD:
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