<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;
use Tagcade\Domain\DTO\Report\UnifiedReport\AverageValue as AverageValueDTO;
use Tagcade\Entity\Report\UnifiedReport\PulsePoint\AdTagDomainImpression as AdTagDomainImpressionEntity;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class AdTagDomainImpressionRepository extends AbstractReportRepository implements AdTagDomainImpressionRepositoryInterface
{
    // search fields
    const AD_TAG_DOMAIN_IMP_PUBLISHER_FIELD = "publisherId";
    const AD_TAG_DOMAIN_IMP_DOMAIN_FIELD = "domain";
    const AD_TAG_DOMAIN_IMP_AD_TAG_ID_FIELD = "adTagId";
    const AD_TAG_DOMAIN_IMP_AD_TAG_FIELD = "adTag";
    const AD_TAG_DOMAIN_IMP_DOMAIN_STATUS_FIELD = "domainStatus";
    // sort fields
    const AD_TAG_DOMAIN_IMP_FILL_RATE_FIELD = "fillRate";
    const AD_TAG_DOMAIN_IMP_PAID_IMPS_FIELD = "paidImps";
    const AD_TAG_DOMAIN_IMP_TOTAL_IMPS_FIELD = "totalImps";
    const AD_TAG_DOMAIN_IMP_DATE_FIELD = "date";
    // sort direction
    const SORT_DIRECTION_ASC = "asc";
    const SORT_DIRECTION_DESC = "desc";

    /**
     * @inheritdoc
     */
    public function getAverageValues(PublisherInterface $publisher, UnifiedReportParams $params)
    {
        $qb = parent::getReportsInRange($params->getStartDate(), $params->getEndDate());

        $result = $qb
            ->addSelect('(SUM(r.fillRate * r.paidImps) / SUM(r.paidImps)) as fillRate')
            ->addSelect('AVG(r.fillRate) as averageFillRate')
            ->addSelect('SUM(r.paidImps) as paidImps')
            ->addSelect('AVG(r.paidImps) as averagePaidImps')
            ->addSelect('SUM(r.totalImps) as totalImps')
            ->addSelect('AVG(r.totalImps) as averageTotalImps')
            ->andWhere('r.publisherId = :publisherId')
            ->setParameter('publisherId', $publisher->getId())
            ->getQuery()
            ->getResult();

        if (is_array($result)) {
            $result = array_map(function ($rst) {
                return (is_array($rst) && count($rst) > 6)
                    ? (new AverageValueDTO())
                        ->setFillRate(is_numeric($rst['fillRate']) ? round($rst['fillRate'], 4) : null)
                        ->setPaidImps($rst['paidImps'])
                        ->setTotalImps($rst['totalImps'])
                        ->setAverageFillRate(is_numeric($rst['averageFillRate']) ? round($rst['averageFillRate'], 4) : null)
                        ->setAveragePaidImps(is_numeric($rst['averagePaidImps']) ? round($rst['averagePaidImps'], 0) : null)
                        ->setAverageTotalImps(is_numeric($rst['averageTotalImps']) ? round($rst['averageTotalImps'], 0) : null)
                    : null;
            }, $result);
        }

        return current($result);
    }

    /**
     * @inheritdoc
     */
    public function getCount(PublisherInterface $publisher, UnifiedReportParams $params)
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
                    case self::AD_TAG_DOMAIN_IMP_AD_TAG_FIELD :
                        $nestedQuery .= empty($nestedQuery) ? ' r.adTag LIKE :ad_tag' : ' OR r.adTag LIKE :ad_tag';
                        break;
                    case self::AD_TAG_DOMAIN_IMP_DOMAIN_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.adTag LIKE :ad_tag' : ' OR r.adTag LIKE :ad_tag';
                        break;
                    case self::AD_TAG_DOMAIN_IMP_AD_TAG_ID_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.adTagId = :ad_tag_id' : ' OR r.adTagId = :ad_tag_id';
                        break;
                    case self::AD_TAG_DOMAIN_IMP_PUBLISHER_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.publisherId = :publisher_id' : ' OR r.publisherId = :publisher_id';
                        break;
                    case self::AD_TAG_DOMAIN_IMP_DOMAIN_STATUS_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.domainStatus = :status' : ' OR r.domainStatus = :status';
                        break;
                }
            }

            $qb->andWhere($nestedQuery);

            foreach ($searchField as $field) {
                switch ($field) {
                    case self::AD_TAG_DOMAIN_IMP_AD_TAG_FIELD :
                        $qb->setParameter('ad_tag', '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::AD_TAG_DOMAIN_IMP_DOMAIN_FIELD:
                        $qb->setParameter('domain', '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::AD_TAG_DOMAIN_IMP_AD_TAG_ID_FIELD:
                        $qb->setParameter('ad_tag_id', intval($searchKey), Type::INTEGER);
                        break;
                    case self::AD_TAG_DOMAIN_IMP_PUBLISHER_FIELD:
                        $qb->setParameter('publisher_id', intval($searchKey), Type::INTEGER);
                        break;
                    case self::AD_TAG_DOMAIN_IMP_DOMAIN_STATUS_FIELD:
                        $qb->setParameter('status', $searchKey, Type::STRING);
                        break;
                }
            }

        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @inheritdoc
     */
    public function getItems(PublisherInterface $publisher, UnifiedReportParams $params, $defaultPageSize = 10)
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
        $rsm->addEntityResult(AdTagDomainImpressionEntity::class, 'a');
        $rsm->addFieldResult('a', 'id', 'id');
        $rsm->addFieldResult('a', 'domain', 'domain');
        $rsm->addFieldResult('a', 'domain_status', 'domainStatus');
        $rsm->addFieldResult('a', 'publisher_id', 'publisherId');
        $rsm->addFieldResult('a', 'total_imps', 'totalImps');
        $rsm->addFieldResult('a', 'paid_imps', 'paidImps');
        $rsm->addFieldResult('a', 'fill_rate', 'fillRate');
        $rsm->addFieldResult('a', 'ad_tag', 'adTag');
        $rsm->addFieldResult('a', 'ad_tag_id', 'adTagId');
        $rsm->addFieldResult('a', 'date', 'date');

        $selectQuery = "SELECT id, domain, domain_status, total_imps, paid_imps, fill_rate, ad_tag, ad_tag_id, date FROM report_pulse_point_ad_tag_domain_impression INNER JOIN ";
        $mainQuery = "SELECT id FROM report_pulse_point_ad_tag_domain_impression WHERE (date BETWEEN :start_date AND :end_date) AND publisher_id = :publisher_id";

        $firstCondition = true;
        if (is_array($searchField) && $searchKey !== null) {
            $mainQuery .= " AND (";
            foreach ($searchField as $field) {
                switch ($field) {
                    case self::AD_TAG_DOMAIN_IMP_AD_TAG_FIELD :
                        $mainQuery .= $firstCondition ? ' ad_tag LIKE :ad_tag' : ' OR ad_tag LIKE :ad_tag';
                        $firstCondition = false;
                        break;
                    case self::AD_TAG_DOMAIN_IMP_DOMAIN_FIELD:
                        $mainQuery .= $firstCondition ? ' domain LIKE :domain' : ' OR domain LIKE :domain';
                        $firstCondition = false;
                        break;
                    case self::AD_TAG_DOMAIN_IMP_AD_TAG_ID_FIELD:
                        $mainQuery .= $firstCondition ? ' ad_tag_id = :ad_tag_id' : ' OR ad_tag_id = :ad_tag_id';
                        $firstCondition = false;
                        break;
                    case self::AD_TAG_DOMAIN_IMP_PUBLISHER_FIELD:
                        $mainQuery .= $firstCondition ? ' publisher_id = :publisher_id' : ' OR publisher_id = :publisher_id';
                        $firstCondition = false;
                        break;
                    case self::AD_TAG_DOMAIN_IMP_DOMAIN_STATUS_FIELD:
                        $mainQuery .= $firstCondition ? ' domain_status = :status' : ' OR domain_status = :status';
                        $firstCondition = false;
                        break;
                }
            }

            $mainQuery .= ")";
        }

        if ($sortField !== null && $sortDirection !== null &&
            in_array($sortDirection, [self::SORT_DIRECTION_ASC, self::SORT_DIRECTION_DESC]) &&
            in_array($sortField, [self::AD_TAG_DOMAIN_IMP_FILL_RATE_FIELD, self::AD_TAG_DOMAIN_IMP_PAID_IMPS_FIELD, self::AD_TAG_DOMAIN_IMP_TOTAL_IMPS_FIELD, self::AD_TAG_DOMAIN_IMP_DATE_FIELD])
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
                    case self::AD_TAG_DOMAIN_IMP_AD_TAG_FIELD :
                        $query->setParameter("ad_tag", '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::AD_TAG_DOMAIN_IMP_DOMAIN_FIELD:
                        $query->setParameter("domain", '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::AD_TAG_DOMAIN_IMP_AD_TAG_ID_FIELD:
                        $query->setParameter("ad_tag_id", intval($searchKey), Type::INTEGER);
                        break;
                    case self::AD_TAG_DOMAIN_IMP_PUBLISHER_FIELD:
                        $query->setParameter("publisher_id", intval($searchKey), Type::INTEGER);
                        break;
                    case self::AD_TAG_DOMAIN_IMP_DOMAIN_STATUS_FIELD:
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
}