<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;
use Tagcade\Domain\DTO\Report\UnifiedReport\AverageValue as AverageValueDTO;
use Tagcade\Entity\Report\UnifiedReport\PulsePoint\CountryDaily;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class CountryDailyRepository extends AbstractReportRepository implements CountryDailyRepositoryInterface
{
    // search fields
    const COUNTRY_DAILY_PUBLISHER_FIELD = "publisherId";
    const COUNTRY_DAILY_TAG_ID_FIELD = "tagId";
    const COUNTRY_DAILY_AD_TAG_NAME_FIELD = "adTagName";
    const COUNTRY_DAILY_AD_TAG_GROUP_ID_FIELD = "adTagGroupId";
    const COUNTRY_DAILY_AD_TAG_GROUP_NAME_FIELD = "adTagGroupName";
    const COUNTRY_DAILY_COUNTRY_FIELD = "country";
    const COUNTRY_DAILY_COUNTRY_NAME_FIELD = "countryName";
    // sort fields
    const COUNTRY_DAILY_FILL_RATE_FIELD = "fillRate";
    const COUNTRY_DAILY_PAID_IMPS_FIELD = "paidImpressions";
    const COUNTRY_DAILY_ALL_IMPS_FIELD = "allImpressions";
    const COUNTRY_DAILY_PUB_PAYOUT_FIELD = "pubPayout";
    const COUNTRY_DAILY_CPM_FIELD = "cpm";
    // sort direction
    // sort direction
    const SORT_DIRECTION_ASC = "asc";
    const SORT_DIRECTION_DESC = "desc";

    /**
     * override because differ from r.day (parents: r.date)
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getReportsInRange(\DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        return $qb
            ->andWhere($qb->expr()->between('r.day', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->addOrderBy('r.tagId', 'ASC')
            ->addOrderBy('r.country', 'ASC')
            ->addOrderBy('r.day', 'ASC');
    }

    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @return int
     */
    protected function getTotalRecordsForAdTagGroupCountry(PublisherInterface $publisher, UnifiedReportParams $params)
    {
        $searchField = $params->getSearchField();
        $searchKey = $params->getSearchKey();

        $qb = $this->createQueryBuilder('r')->select('count(r.id) as total');

        $qb
            ->andWhere($qb->expr()->between('r.day', ':start_date', ':end_date'))
            ->andWhere('r.publisherId = :publisherId')
            ->setParameter('start_date', $params->getStartDate(), Type::DATE)
            ->setParameter('end_date', $params->getEndDate(), Type::DATE)
            ->setParameter('publisherId', $publisher->getId());

        $nestedQuery = '';

        if (is_array($searchField) && $searchKey !== null) {
            foreach ($searchField as $field) {
                switch ($field) {
                    case self::COUNTRY_DAILY_AD_TAG_NAME_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.adTagName LIKE :ad_tag_name' : ' OR r.adTagName LIKE :ad_tag_name';
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_NAME_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.adTagGroupName LIKE :ad_tag_group_name' : ' OR r.adTagGroupName LIKE :ad_tag_group_name';
                        break;
                    case self::COUNTRY_DAILY_TAG_ID_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.tagId = :tag_id' : ' OR r.tagId = :tag_id';
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_ID_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.adTagGroupId = :ad_tag_group_id' : ' OR r.adTagGroupId = :ad_tag_group_id';
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.country LIKE :country' : ' OR r.country LIKE :country';
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_NAME_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.countryName LIKE :country_name' : ' OR r.countryName LIKE :country_name';
                        break;
                }
            }

            $qb->andWhere($nestedQuery);

            foreach ($searchField as $field) {
                switch ($field) {
                    case self::COUNTRY_DAILY_AD_TAG_NAME_FIELD:
                        $qb->setParameter('ad_tag_name', '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_NAME_FIELD:
                        $qb->setParameter('ad_tag_group_name', '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::COUNTRY_DAILY_TAG_ID_FIELD:
                        $qb->setParameter('tag_id', intval($searchKey), Type::INTEGER);
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_ID_FIELD:
                        $qb->setParameter('ad_tag_group_id', intval($searchKey), Type::INTEGER);
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_FIELD:
                        $qb->setParameter('country', '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_NAME_FIELD:
                        $qb->setParameter('country_name', '%' . $searchKey . '%', Type::STRING);
                        break;
                }
            }
        }

        $qb->addGroupBy('r.adTagGroupId, r.country');

        return count($qb->getQuery()->getScalarResult());
    }

    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @param int $defaultPageSize
     * @return array|bool
     */
    protected function getPaginationRecordsForAdTagGroupCountry(PublisherInterface $publisher, UnifiedReportParams $params, $defaultPageSize = 10)
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
        $rsm->addScalarResult('tagId', 'tagId');
        $rsm->addScalarResult('adTagName', 'adTagName');
        $rsm->addScalarResult('adTagGroupId', 'adTagGroupId');
        $rsm->addScalarResult('adTagGroupName', 'adTagGroupName');
        $rsm->addScalarResult('country', 'country');
        $rsm->addScalarResult('countryName', 'countryName');
        $rsm->addScalarResult('allImpressions', 'allImpressions');
        $rsm->addScalarResult('paidImpressions', 'paidImpressions');
        $rsm->addScalarResult('fillRate', 'fillRate');
        $rsm->addScalarResult('cpm', 'cpm');
        $rsm->addScalarResult('pubPayout', 'pubPayout');
        $rsm->addScalarResult('day', 'day');

        $selectQuery = "SELECT
                            id as id,
                            publisher_id as publisherId,
                            tag_id as tagId,
                            ad_tag_name as adTagName,
                            ad_tag_group_id as adTagGroupId,
                            ad_tag_group_name as adTagGroupName,
                            country as country,
                            country_name as countryName,
                            fill_rate as fillRate,
                            all_impressions as allImpressions,
                            paid_impressions as paidImpressions,
                            cpm as cpm,
                            pub_payout as pubPayout,
                            day as day
                       FROM report_pulse_point_country_daily INNER JOIN ";
        $mainQuery = "SELECT id FROM report_pulse_point_country_daily WHERE (day BETWEEN :start_date AND :end_date) AND publisher_id = :publisher_id";
        $groupQuery = 'SELECT
                          r.id,
                          r.publisherId as publisherId,
                          r.tagId as tagId,
                          r.adTagName as adTagName,
                          r.adTagGroupName as adTagGroupName,
                          r.adTagGroupId as adTagGroupId,
                          r.country as country,
                          r.countryName as countryName,
                          r.day as day,
                          (SUM(r.fillRate * r.paidImpressions) / SUM(r.paidImpressions)) as fillRate,
                          (SUM(r.cpm * r.pubPayout) / SUM(r.pubPayout)) as cpm,
                          SUM(r.paidImpressions) as paidImpressions,
                          SUM(r.allImpressions) as allImpressions,
                          SUM(r.pubPayout) as pubPayout
                       FROM (%s) as r
                       GROUP BY r.adTagGroupId, r.country ';
        $orderQuery = "SELECT * FROM (%s) as r1";
        $firstCondition = true;
        if (is_array($searchField) && $searchKey !== null) {
            $mainQuery .= " AND (";
            foreach ($searchField as $field) {
                switch ($field) {
                    case self::COUNTRY_DAILY_AD_TAG_NAME_FIELD:
                        $mainQuery .= $firstCondition ? ' ad_tag_name LIKE :ad_tag_name' : ' OR ad_tag_name LIKE :ad_tag_name';
                        $firstCondition = false;
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_NAME_FIELD:
                        $mainQuery .= $firstCondition ? ' ad_tag_group_name LIKE :ad_tag_group_name' : ' OR ad_tag_group_name LIKE :ad_tag_group_name';
                        $firstCondition = false;
                        break;
                    case self::COUNTRY_DAILY_TAG_ID_FIELD:
                        $mainQuery .= $firstCondition ? ' tag_id = :tag_id' : ' OR tag_id = :tag_id';
                        $firstCondition = false;
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_ID_FIELD:
                        $mainQuery .= $firstCondition ? ' ad_tag_group_id = :ad_tag_group_id' : ' OR ad_tag_group_id = :ad_tag_group_id';
                        $firstCondition = false;
                        break;
                    case self::COUNTRY_DAILY_PUBLISHER_FIELD:
                        $mainQuery .= $firstCondition ? ' publisher_id = :publisher_id' : ' OR publisher_id = :publisher_id';
                        $firstCondition = false;
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_FIELD:
                        $mainQuery .= $firstCondition ? ' country LIKE :country' : ' OR country LIKE :country';
                        $firstCondition = false;
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_NAME_FIELD:
                        $mainQuery .= $firstCondition ? ' country_name LIKE :country_name' : ' OR country_name LIKE :country_name';
                        $firstCondition = false;
                        break;
                }
            }

            $mainQuery .= ")";
        }

        $mainQuery = "(" . $mainQuery . ") as temp_tbl";

        if ($sortField !== null && $sortDirection !== null &&
            in_array($sortDirection, [self::SORT_DIRECTION_ASC, self::SORT_DIRECTION_DESC]) &&
            in_array($sortField, [self::COUNTRY_DAILY_FILL_RATE_FIELD, self::COUNTRY_DAILY_PAID_IMPS_FIELD, self::COUNTRY_DAILY_ALL_IMPS_FIELD, self::COUNTRY_DAILY_PUB_PAYOUT_FIELD, self::COUNTRY_DAILY_CPM_FIELD])
        ) {
            $orderQuery .= sprintf(" ORDER BY %s %s", 'r1.'.$sortField, $sortDirection);
        }
        else {
            $orderQuery .= " ORDER BY r1.adTagGroupId, r1.country";
        }

        $orderQuery .= ' LIMIT :limit OFFSET :off_set';
        $groupQuery = sprintf($groupQuery, $selectQuery . $mainQuery . " using (id)");
        $orderQuery = sprintf($orderQuery, $groupQuery);
        $query = $this->getEntityManager()->createNativeQuery($orderQuery , $rsm);

        if (is_array($searchField) && $searchKey !== null) {
            foreach ($searchField as $field) {
                switch ($field) {
                    case self::COUNTRY_DAILY_AD_TAG_NAME_FIELD:
                        $query->setParameter("ad_tag_name", '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_NAME_FIELD:
                        $query->setParameter("ad_tag_group_name", '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::COUNTRY_DAILY_TAG_ID_FIELD:
                        $query->setParameter("tag_id", intval($searchKey), Type::INTEGER);
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_ID_FIELD:
                        $query->setParameter("ad_tag_group_id", intval($searchKey), Type::INTEGER);
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_FIELD:
                        $query->setParameter("country", '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_NAME_FIELD:
                        $query->setParameter("country_name", '%' . $searchKey . '%', Type::STRING);
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
            return (is_array($rst) && count($rst) > 13)
                ? (new CountryDaily())
                    ->setId(intval($rst['id']))
                    ->setPublisherId(intval($rst['publisherId']))
                    ->setTagId(intval($rst['tagId']))
                    ->setAdTagName($rst['adTagName'])
                    ->setAdTagGroupId(intval($rst['adTagGroupId']))
                    ->setAdTagGroupName($rst['adTagGroupName'])
                    ->setCountry($rst['country'])
                    ->setCountryName($rst['countryName'])
                    ->setFillRate(is_numeric($rst['fillRate']) ? round($rst['fillRate'], 4) : 0)
                    ->setPaidImps(intval($rst['paidImpressions']))
                    ->setTotalImps(intval($rst['allImpressions']))
                    ->setPubPayout(is_numeric($rst['pubPayout']) ? round($rst['pubPayout'], 4) : 0)
                    ->setCpm(is_numeric($rst['cpm']) ? round($rst['cpm'], 4) : 0)
                    ->setDay((bool)strtotime($rst['day']) ? new \DateTime($rst['day']) : null)
                : null;
        }, $result);
    }

    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @return $this|bool
     */
    protected function getAverageValuesForAdTagGroupCountry(PublisherInterface $publisher, UnifiedReportParams $params)
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('paidImpressions', 'paidImpressions');
        $rsm->addScalarResult('allImpressions', 'allImpressions');
        $rsm->addScalarResult('pubPayout', 'pubPayout');
        $rsm->addScalarResult('fillRate', 'fillRate');
        $rsm->addScalarResult('cpm', 'cpm');
        $rsm->addScalarResult('averagePaidImpressions', 'averagePaidImpressions');
        $rsm->addScalarResult('averageAllImpressions', 'averageAllImpressions');
        $rsm->addScalarResult('averagePubPayout', 'averagePubPayout');
        $rsm->addScalarResult('averageFillRate', 'averageFillRate');
        $rsm->addScalarResult('averageCpm', 'averageCpm');

        $mainQuery = 'SELECT
                            id,
                            SUM(paid_impressions) as paidImpressions,
                            SUM(all_impressions) as allImpressions,
                            SUM(pub_payout) as pubPayout,
                            (SUM(fill_rate * paid_impressions) / SUM(paid_impressions)) as fillRate,
                            (SUM(cpm * pub_payout) / SUM(pub_payout)) as cpm
                            FROM report_pulse_point_country_daily
                      WHERE ( day BETWEEN :start_date AND :end_date) AND publisher_id = :publisher_id GROUP BY ad_tag_group_id, country';
        $select = 'SELECT
                        r.id,
                        (SUM(r.fillRate * r.paidImpressions) / SUM(r.paidImpressions)) as fillRate,
                        AVG(r.fillRate) as averageFillRate,
                        SUM(r.paidImpressions) as paidImpressions,
                        AVG(r.paidImpressions) as averagePaidImpressions,
                        SUM(r.allImpressions) as allImpressions,
                        AVG(r.allImpressions) as averageAllImpressions,
                        SUM(r.pubPayout) as pubPayout,
                        AVG(r.pubPayout) as averagePubPayout,
                        (SUM(r.cpm * r.pubPayout) / SUM(r.pubPayout)) as cpm,
                        AVG(r.cpm) as averageCpm
                        FROM (%s) as r';
        $nativeQuery = $this->getEntityManager()->createNativeQuery(sprintf($select, $mainQuery), $rsm);
        $nativeQuery->setParameter('start_date', $params->getStartDate(), Type::DATE);
        $nativeQuery->setParameter('end_date', $params->getEndDate(), Type::DATE);
        $nativeQuery->setParameter('publisher_id', $publisher->getId(), Type::INTEGER);

        $rst = current($nativeQuery->getScalarResult());

        if (!is_array($rst) || count($rst) < 11) {
            return false;
        }

        return (new AverageValueDTO())
            ->setFillRate(is_numeric($rst['fillRate']) ? round($rst['fillRate'], 4) : 0)
            ->setAverageFillRate(is_numeric($rst['averageFillRate']) ? round($rst['averageFillRate'], 4) : 0)
            ->setPaidImps($rst['paidImpressions'])
            ->setAveragePaidImps(is_numeric($rst['averagePaidImpressions']) ? round($rst['averagePaidImpressions'], 0) : 0)
            ->setTotalImps(intval($rst['allImpressions']))
            ->setAverageTotalImps(is_numeric($rst['averageAllImpressions']) ? round($rst['averageAllImpressions'], 0) : 0)
            ->setCpm(is_numeric($rst['cpm']) ? round($rst['cpm'], 4) : 0)
            ->setAverageCpm(is_numeric($rst['averageCpm']) ? round($rst['averageCpm'], 4) : 0)
            ->setPubPayout(is_numeric($rst['pubPayout']) ? round($rst['pubPayout'], 4) : 0)
            ->setAveragePubPayout(is_numeric($rst['averagePubPayout']) ? round($rst['averagePubPayout'], 4) : 0);
    }


    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @return mixed
     */
    protected function getAverageValues(PublisherInterface $publisher, UnifiedReportParams $params)
    {
        $qb = $this->getReportsInRange($params->getStartDate(), $params->getEndDate());

        $result = $qb
            ->addSelect('(SUM(r.fillRate * r.paidImpressions) / SUM(r.paidImpressions)) as fillRate')
            ->addSelect('AVG(r.fillRate) as averageFillRate')
            ->addSelect('SUM(r.paidImpressions) as paidImpressions')
            ->addSelect('AVG(r.paidImpressions) as averagePaidImpressions')
            ->addSelect('SUM(r.allImpressions) as allImpressions')
            ->addSelect('AVG(r.allImpressions) as averageAllImpressions')
            ->addSelect('SUM(r.pubPayout) as pubPayout')
            ->addSelect('AVG(r.pubPayout) as averagePubPayout')
            ->addSelect('(SUM(r.cpm * r.pubPayout) / SUM(r.pubPayout)) as cpm')
            ->addSelect('AVG(r.cpm) as averageCpm')
            ->andWhere('r.publisherId = :publisherId')
            ->setParameter('publisherId', $publisher->getId())
            ->getQuery()
            ->getResult();

        if (is_array($result)) {
            $result = array_map(function ($rst) {
                return (is_array($rst) && count($rst) > 10)
                    ? (new AverageValueDTO())
                        ->setFillRate(is_numeric($rst['fillRate']) ? round($rst['fillRate'], 4) : 0)
                        ->setPaidImps(intval($rst['paidImpressions']))
                        ->setTotalImps(intval($rst['allImpressions']))
                        ->setAvgCpm(is_numeric($rst['cpm']) ? round($rst['cpm'], 4) : 0)
                        ->setAverageFillRate(is_numeric($rst['averageFillRate']) ? round($rst['averageFillRate'], 4) : 0)
                        ->setAveragePaidImps(is_numeric($rst['averagePaidImpressions']) ? round($rst['averagePaidImpressions'], 0) : 0)
                        ->setAverageTotalImps(is_numeric($rst['averageAllImpressions']) ? round($rst['averageAllImpressions'], 0) : 0)
                        ->setAverageAvgCpm(is_numeric($rst['averageCpm']) ? round($rst['averageCpm'], 4) : 0)
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
            ->andWhere($qb->expr()->between('r.day', ':start_date', ':end_date'))
            ->andWhere('r.publisherId = :publisherId')
            ->setParameter('start_date', $params->getStartDate(), Type::DATE)
            ->setParameter('end_date', $params->getEndDate(), Type::DATE)
            ->setParameter('publisherId', $publisher->getId());

        $nestedQuery = '';

        if (is_array($searchField) && $searchKey !== null) {
            foreach ($searchField as $field) {
                switch ($field) {
                    case self::COUNTRY_DAILY_AD_TAG_NAME_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.adTagName LIKE :ad_tag_name' : ' OR r.adTagName LIKE :ad_tag_name';
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_NAME_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.adTagGroupName LIKE :ad_tag_group_name' : ' OR r.adTagGroupName LIKE :ad_tag_group_name';
                        break;
                    case self::COUNTRY_DAILY_TAG_ID_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.tagId = :tag_id' : ' OR r.tagId = :tag_id';
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_ID_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.adTagGroupId = :ad_tag_group_id' : ' OR r.adTagGroupId = :ad_tag_group_id';
                        break;
                    case self::COUNTRY_DAILY_PUBLISHER_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.publisherId = :publisher_id' : ' OR r.publisherId = :publisher_id';
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.country LIKE :country' : ' OR r.country LIKE :country';
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_NAME_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.countryName LIKE :country_name' : ' OR r.countryName LIKE :country_name';
                        break;
                }
            }

            $qb->andWhere($nestedQuery);

            foreach ($searchField as $field) {
                switch ($field) {
                    case self::COUNTRY_DAILY_AD_TAG_NAME_FIELD:
                        $qb->setParameter('ad_tag_name', '%' . $searchKey . '%');
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_NAME_FIELD:
                        $qb->setParameter('ad_tag_group_name', '%' . $searchKey . '%');
                        break;
                    case self::COUNTRY_DAILY_TAG_ID_FIELD:
                        $qb->setParameter('tag_id', intval($searchKey), Type::INTEGER);
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_ID_FIELD:
                        $qb->setParameter('ad_tag_group_id', intval($searchKey), Type::INTEGER);
                        break;
                    case self::COUNTRY_DAILY_PUBLISHER_FIELD:
                        $qb->setParameter('publisher_id', intval($searchKey), Type::INTEGER);
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_FIELD:
                        $qb->setParameter('country', '%' . $searchKey . '%');
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_NAME_FIELD:
                        $qb->setParameter('country_name', '%' . $searchKey . '%');
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
        $rsm->addEntityResult(CountryDaily::class, 'a');
        $rsm->addFieldResult('a', 'id', 'id');
        $rsm->addFieldResult('a', 'tag_id', 'tagId');
        $rsm->addFieldResult('a', 'day', 'day');
        $rsm->addFieldResult('a', 'ad_tag_name', 'adTagName');
        $rsm->addFieldResult('a', 'publisher_id', 'publisherId');
        $rsm->addFieldResult('a', 'ad_tag_group_id', 'adTagGroupId');
        $rsm->addFieldResult('a', 'ad_tag_group_name', 'adTagGroupName');
        $rsm->addFieldResult('a', 'country', 'country');
        $rsm->addFieldResult('a', 'country_name', 'countryName');
        $rsm->addFieldResult('a', 'all_impressions', 'allImpressions');
        $rsm->addFieldResult('a', 'paid_impressions', 'paidImpressions');
        $rsm->addFieldResult('a', 'fill_rate', 'fillRate');
        $rsm->addFieldResult('a', 'cpm', 'cpm');
        $rsm->addFieldResult('a', 'pub_payout', 'pubPayout');

        $selectQuery = "SELECT id, day, tag_id, ad_tag_name, fill_rate, ad_tag_group_id, ad_tag_group_name, country, country_name, all_impressions, paid_impressions, cpm , pub_payout FROM report_pulse_point_country_daily INNER JOIN ";
        $mainQuery = "SELECT id FROM report_pulse_point_country_daily WHERE (day BETWEEN :start_date AND :end_date) AND publisher_id = :publisher_id";

        $firstCondition = true;
        if (is_array($searchField) && $searchKey !== null) {
            $mainQuery .= " AND (";
            foreach ($searchField as $field) {
                switch ($field) {
                    case self::COUNTRY_DAILY_AD_TAG_NAME_FIELD:
                        $mainQuery .= $firstCondition ? ' ad_tag_name LIKE :ad_tag_name' : ' OR ad_tag_name LIKE :ad_tag_name';
                        $firstCondition = false;
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_NAME_FIELD:
                        $mainQuery .= $firstCondition ? ' ad_tag_group_name LIKE :ad_tag_group_name' : ' OR ad_tag_group_name LIKE :ad_tag_group_name';
                        $firstCondition = false;
                        break;
                    case self::COUNTRY_DAILY_TAG_ID_FIELD:
                        $mainQuery .= $firstCondition ? ' tag_id = :tag_id' : ' OR tag_id = :tag_id';
                        $firstCondition = false;
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_ID_FIELD:
                        $mainQuery .= $firstCondition ? ' ad_tag_group_id = :ad_tag_group_id' : ' OR ad_tag_group_id = :ad_tag_group_id';
                        $firstCondition = false;
                        break;
                    case self::COUNTRY_DAILY_PUBLISHER_FIELD:
                        $mainQuery .= $firstCondition ? ' publisher_id = :publisher_id' : ' OR publisher_id = :publisher_id';
                        $firstCondition = false;
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_FIELD:
                        $mainQuery .= $firstCondition ? ' country LIKE :country' : ' OR country LIKE :country';
                        $firstCondition = false;
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_NAME_FIELD:
                        $mainQuery .= $firstCondition ? ' country_name LIKE :country_name' : ' OR country_name LIKE :country_name';
                        $firstCondition = false;
                        break;
                }
            }

            $mainQuery .= ")";
        }

        if ($sortField !== null && $sortDirection !== null &&
            in_array($sortDirection, [self::SORT_DIRECTION_ASC, self::SORT_DIRECTION_DESC]) &&
            in_array($sortField, [self::COUNTRY_DAILY_FILL_RATE_FIELD, self::COUNTRY_DAILY_PAID_IMPS_FIELD, self::COUNTRY_DAILY_ALL_IMPS_FIELD, self::COUNTRY_DAILY_PUB_PAYOUT_FIELD, self::COUNTRY_DAILY_CPM_FIELD])
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
                    case self::COUNTRY_DAILY_AD_TAG_NAME_FIELD:
                        $query->setParameter("ad_tag_name", '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_NAME_FIELD:
                        $query->setParameter("ad_tag_group_name", '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::COUNTRY_DAILY_TAG_ID_FIELD:
                        $query->setParameter("tag_id", intval($searchKey), Type::INTEGER);
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_ID_FIELD:
                        $query->setParameter("ad_tag_group_id", intval($searchKey), Type::INTEGER);
                        break;
                    case self::COUNTRY_DAILY_PUBLISHER_FIELD:
                        $query->setParameter("publisher_id", intval($searchKey), Type::INTEGER);
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_FIELD:
                        $query->setParameter("country", '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_NAME_FIELD:
                        $query->setParameter("country_name", '%' . $searchKey . '%', Type::STRING);
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
     * @return $this|bool
     */
    protected function getAverageValuesForAdTagCountry(PublisherInterface $publisher, UnifiedReportParams $params)
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('allImpressions', 'allImpressions');
        $rsm->addScalarResult('paidImpressions', 'paidImpressions');
        $rsm->addScalarResult('fillRate', 'fillRate');
        $rsm->addScalarResult('cpm', 'cpm');
        $rsm->addScalarResult('pubPayout', 'pubPayout');
        $rsm->addScalarResult('averageAllImpressions', 'averageAllImpressions');
        $rsm->addScalarResult('averagePaidImpressions', 'averagePaidImpressions');
        $rsm->addScalarResult('averageFillRate', 'averageFillRate');
        $rsm->addScalarResult('averageCpm', 'averageCpm');
        $rsm->addScalarResult('averagePubPayout', 'averagePubPayout');

        $mainQuery = 'SELECT
                          id,
                          SUM(paid_impressions) as paidImpressions,
                          SUM(all_impressions) as allImpressions,
                          (SUM(fill_rate * paid_impressions) / SUM(paid_impressions)) as fillRate,
                          (SUM(cpm * pub_payout) / SUM(pub_payout)) as cpm,
                          SUM(pub_payout) as pubPayout
                      FROM report_pulse_point_country_daily
                      WHERE
                          (day BETWEEN :start_date AND :end_date)
                          AND publisher_id = :publisher_id
                      GROUP BY tag_id, country';
        $select = 'SELECT
                      id,
                      (SUM(r.fillRate * r.paidImpressions) / SUM(r.paidImpressions)) as fillRate,
                      AVG(r.fillRate) as averageFillRate,
                      SUM(r.paidImpressions) as paidImpressions,
                      AVG(r.paidImpressions) as averagePaidImpressions,
                      SUM(r.allImpressions) as allImpressions,
                      AVG(r.allImpressions) as averageAllImpressions,
                      (SUM(r.cpm * r.pubPayout) / SUM(r.pubPayout)) as cpm,
                      AVG(r.cpm) as averageCpm,
                      SUM(r.pubPayout) as pubPayout,
                      AVG(r.pubPayout) as averagePubPayout
                   FROM (%s) as r';
        $nativeQuery = $this->getEntityManager()->createNativeQuery(sprintf($select, $mainQuery), $rsm);
        $nativeQuery->setParameter('start_date', $params->getStartDate(), Type::DATE);
        $nativeQuery->setParameter('end_date', $params->getEndDate(), Type::DATE);
        $nativeQuery->setParameter('publisher_id', $publisher->getId(), Type::INTEGER);

        $rst = current($nativeQuery->getScalarResult());

        if (!is_array($rst) || count($rst) < 9) {
            return false;
        }

        return (new AverageValueDTO())
            ->setFillRate(is_numeric($rst['fillRate']) ? round($rst['fillRate'], 4) : null)
            ->setPaidImps($rst['paidImpressions'])
            ->setTotalImps($rst['allImpressions'])
            ->setCpm($rst['cpm'])
            ->setPubPayout($rst['pubPayout'])
            ->setAverageFillRate(is_numeric($rst['averageFillRate']) ? round($rst['averageFillRate'], 4) : null)
            ->setAveragePaidImps(is_numeric($rst['averagePaidImpressions']) ? round($rst['averagePaidImpressions'], 0) : null)
            ->setAverageTotalImps(is_numeric($rst['averageAllImpressions']) ? round($rst['averageAllImpressions'], 0) : null)
            ->setAverageCpm(is_numeric($rst['averageCpm']) ? round($rst['averageCpm'], 4) : null)
            ->setAveragePubPayout(is_numeric($rst['averagePubPayout']) ? round($rst['averagePubPayout'], 4) : null);
    }

    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @return int
     */
    protected function getTotalRecordsForAdTagCountry(PublisherInterface $publisher, UnifiedReportParams $params)
    {
        $searchField = $params->getSearchField();
        $searchKey = $params->getSearchKey();

        $qb = $this->createQueryBuilder('r')->select('count(r.id) as total');

        $qb
            ->andWhere($qb->expr()->between('r.day', ':start_date', ':end_date'))
            ->andWhere('r.publisherId = :publisherId')
            ->setParameter('start_date', $params->getStartDate(), Type::DATE)
            ->setParameter('end_date', $params->getEndDate(), Type::DATE)
            ->setParameter('publisherId', $publisher->getId());

        $nestedQuery = '';

        if (is_array($searchField) && $searchKey !== null) {
            foreach ($searchField as $field) {
                switch ($field) {
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_NAME_FIELD :
                        $nestedQuery .= empty($nestedQuery) ? ' r.adTagGroupName LIKE :ad_tag_group_name' : ' OR r.adTagGroupName LIKE :ad_tag_group_name';
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_ID_FIELD :
                        $nestedQuery .= empty($nestedQuery) ? ' r.adTagGroupId LIKE :ad_tag_group_id' : ' OR r.adTagGroupId LIKE :ad_tag_group_id';
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_NAME_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.adTagName LIKE :ad_tag_name' : ' OR r.adTagName LIKE :ad_tag_name';
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_NAME_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.countryName LIKE :country_name' : ' OR r.countryName LIKE :country_name';
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.country LIKE :country' : ' OR r.country LIKE :country';
                        break;
                    case self::COUNTRY_DAILY_PUBLISHER_FIELD:
                        $nestedQuery .= empty($nestedQuery) ? ' r.publisherId = :publisher_id' : ' OR r.publisherId = :publisher_id';
                        break;
                }
            }

            $qb->andWhere($nestedQuery);

            foreach ($searchField as $field) {
                switch ($field) {
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_NAME_FIELD :
                        $qb->setParameter('ad_tag_group_name', '%' . $searchKey . '%');
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_ID_FIELD :
                        $qb->setParameter('ad_tag_group_id', '%' . $searchKey . '%');
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_NAME_FIELD:
                        $qb->setParameter('ad_tag_name', '%' . $searchKey . '%');
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_NAME_FIELD:
                        $qb->setParameter('country_name', $searchKey, Type::STRING);
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_FIELD:
                        $qb->setParameter('country', $searchKey, Type::STRING);
                        break;
                    case self::COUNTRY_DAILY_PUBLISHER_FIELD:
                        $qb->setParameter('publisher_id', intval($searchKey), Type::INTEGER);
                        break;
                }
            }
        }

        $qb->addGroupBy('r.tagId, r.country');

        return count($qb->getQuery()->getScalarResult());
    }

    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @param int $defaultPageSize
     * @return array|bool
     */
    protected function getPaginationRecordsForAdTagCountry(PublisherInterface $publisher, UnifiedReportParams $params, $defaultPageSize = 10)
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
        $rsm->addScalarResult('day', 'day');
        $rsm->addScalarResult('tagId', 'tagId');
        $rsm->addScalarResult('adTagName', 'adTagName');
        $rsm->addScalarResult('fillRate', 'fillRate');
        $rsm->addScalarResult('adTagGroupId', 'adTagGroupId');
        $rsm->addScalarResult('adTagGroupName', 'adTagGroupName');
        $rsm->addScalarResult('country', 'country');
        $rsm->addScalarResult('countryName', 'countryName');
        $rsm->addScalarResult('allImpressions', 'allImpressions');
        $rsm->addScalarResult('paidImpressions', 'paidImpressions');
        $rsm->addScalarResult('cpm', 'cpm');
        $rsm->addScalarResult('pubPayout', 'pubPayout');

        $selectFromSearchQuery = "SELECT
                            id,
                            publisher_id as publisherId,
                            day as day,
                            tag_id as tagId,
                            ad_tag_name as adTagName,
                            fill_rate as fillRate,
                            ad_tag_group_id as adTagGroupId,
                            ad_tag_group_name as adTagGroupName,
                            country as country,
                            country_name as countryName,
                            all_impressions as allImpressions,
                            paid_impressions as paidImpressions,
                            cpm,
                            pub_payout as pubPayout
                        FROM report_pulse_point_country_daily
                        INNER JOIN ";
        $searchQuery = "SELECT
                          id
                      FROM report_pulse_point_country_daily
                      WHERE
                          (day BETWEEN :start_date AND :end_date)
                          AND publisher_id = :publisher_id ";
        $groupQuery = 'SELECT
                          r.id as id,
                          r.publisherId as publisherId,
                          r.day as day,
                          r.tagId as tagId,
                          r.adTagName as adTagName,
                          (SUM(r.fillRate * r.paidImpressions) / SUM(r.paidImpressions)) as fillRate,
                          r.adTagGroupId as adTagGroupId,
                          r.adTagGroupName as adTagGroupName,
                          r.country as country,
                          r.countryName as countryName,
                          SUM(r.allImpressions) as allImpressions,
                          SUM(r.paidImpressions) as paidImpressions,
                          (SUM(r.cpm * r.pubPayout) / SUM(r.pubPayout)) as cpm,
                          SUM(r.pubPayout) as pubPayout
                       FROM (%s) as r
                       GROUP BY r.tagId, r.country ';
        $finalQuery = 'SELECT * FROM (%s) as r1 ';

        $firstCondition = true;
        if (is_array($searchField) && $searchKey !== null) {
            $searchQuery .= " AND (";
            foreach ($searchField as $field) {
                switch ($field) {
                    case self::COUNTRY_DAILY_AD_TAG_NAME_FIELD:
                        $searchQuery .= $firstCondition ? ' ad_tag_name LIKE :ad_tag_name' : ' OR ad_tag_name LIKE :ad_tag_name';
                        $firstCondition = false;
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_NAME_FIELD:
                        $searchQuery .= $firstCondition ? ' ad_tag_group_name LIKE :ad_tag_group_name' : ' OR ad_tag_group_name LIKE :ad_tag_group_name';
                        $firstCondition = false;
                        break;
                    case self::COUNTRY_DAILY_TAG_ID_FIELD:
                        $searchQuery .= $firstCondition ? ' tag_id = :tag_id' : ' OR tag_id = :tag_id';
                        $firstCondition = false;
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_ID_FIELD:
                        $searchQuery .= $firstCondition ? ' ad_tag_group_id = :ad_tag_group_id' : ' OR ad_tag_group_id = :ad_tag_group_id';
                        $firstCondition = false;
                        break;
                    case self::COUNTRY_DAILY_PUBLISHER_FIELD:
                        $searchQuery .= $firstCondition ? ' publisher_id = :publisher_id' : ' OR publisher_id = :publisher_id';
                        $firstCondition = false;
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_FIELD:
                        $searchQuery .= $firstCondition ? ' country LIKE :country' : ' OR country LIKE :country';
                        $firstCondition = false;
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_NAME_FIELD:
                        $searchQuery .= $firstCondition ? ' country_name LIKE :country_name' : ' OR country_name LIKE :country_name';
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
            in_array($sortField, [self::COUNTRY_DAILY_FILL_RATE_FIELD, self::COUNTRY_DAILY_PAID_IMPS_FIELD, self::COUNTRY_DAILY_ALL_IMPS_FIELD, self::COUNTRY_DAILY_PUB_PAYOUT_FIELD, self::COUNTRY_DAILY_CPM_FIELD])
        ) {
            $finalQuery .= sprintf(" ORDER BY r1.%s %s ", $sortField, $sortDirection);
        }
        else {
            $finalQuery .= " ORDER BY r1.adTagId, r1.country asc ";
        }

        $finalQuery .= "LIMIT :limit OFFSET :off_set ";

        $query = $this->getEntityManager()->createNativeQuery($finalQuery, $rsm);

        if (is_array($searchField) && $searchKey !== null) {
            foreach ($searchField as $field) {
                switch ($field) {
                    case self::COUNTRY_DAILY_AD_TAG_NAME_FIELD:
                        $query->setParameter("ad_tag_name", '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_NAME_FIELD:
                        $query->setParameter("ad_tag_group_name", '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::COUNTRY_DAILY_TAG_ID_FIELD:
                        $query->setParameter("tag_id", intval($searchKey), Type::INTEGER);
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_ID_FIELD:
                        $query->setParameter("ad_tag_group_id", intval($searchKey), Type::INTEGER);
                        break;
                    case self::COUNTRY_DAILY_PUBLISHER_FIELD:
                        $query->setParameter("publisher_id", intval($searchKey), Type::INTEGER);
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_FIELD:
                        $query->setParameter("country", '%' . $searchKey . '%', Type::STRING);
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_NAME_FIELD:
                        $query->setParameter("country_name", '%' . $searchKey . '%', Type::STRING);
                        break;
                }
            }
        }

        $query->setParameter('limit', $params->getSize(), Type::INTEGER);
        $query->setParameter('off_set', $offset, Type::INTEGER);
        $query->setParameter('start_date', $params->getStartDate(), Type::DATE);
        $query->setParameter('end_date', $params->getEndDate(), Type::DATE);
        $query->setParameter('publisher_id', $publisher->getId());

        $result = $query->getScalarResult();

        if (!is_array($result)) {
            return false;
        }

        return array_map(function ($rst) {
            return (is_array($rst) && count($rst) > 12)
                ? (new CountryDaily())
                    ->setId($rst['id'])
                    ->setPublisherId($rst['publisherId'])
                    ->setDay((bool)strtotime($rst['day']) ? new \DateTime($rst['day']) : null)
                    ->setTagId(intval($rst['tagId']))
                    ->setAdTagName($rst['adTagName'])
                    ->setFillRate(is_numeric($rst['fillRate']) ? round($rst['fillRate'], 4) : 0)
                    ->setAdTagGroupId(intval($rst['adTagGroupId']))
                    ->setAdTagGroupName($rst['adTagGroupName'])
                    ->setCountry($rst['country'])
                    ->setCountryName($rst['countryName'])
                    ->setTotalImps(intval($rst['allImpressions']))
                    ->setPaidImps(intval($rst['paidImpressions']))
                    ->setCpm(is_numeric($rst['cpm']) ? round($rst['cpm'], 4) : 0)
                    ->setPubPayout(is_numeric($rst['pubPayout']) ? round($rst['pubPayout'], 4) : 0)
                : null;
        }, $result);
    }

    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @param int $defaultPageSize
     * @return array
     */
    public function getReportsForAdTagGroupCountry(PublisherInterface $publisher, UnifiedReportParams $params, $defaultPageSize = 10)
    {
        return array(
            self::REPORT_AVERAGE_VALUES => $this->getAverageValuesForAdTagGroupCountry($publisher, $params),
            self::REPORT_PAGINATION_RECORDS => $this->getPaginationRecordsForAdTagGroupCountry($publisher, $params, $defaultPageSize),
            self::REPORT_TOTAL_RECORDS => $this->getTotalRecordsForAdTagGroupCountry($publisher, $params)
        );
    }

    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @param int $defaultPageSize
     * @return array
     */
    public function getReportsForAdTagCountry(PublisherInterface $publisher, UnifiedReportParams $params, $defaultPageSize = 10)
    {
        return array(
            self::REPORT_AVERAGE_VALUES => $this->getAverageValuesForAdTagCountry($publisher, $params),
            self::REPORT_PAGINATION_RECORDS => $this->getPaginationRecordsForAdTagCountry($publisher, $params, $defaultPageSize),
            self::REPORT_TOTAL_RECORDS => $this->getTotalRecordsForAdTagCountry($publisher, $params)
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