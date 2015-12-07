<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;


use Doctrine\DBAL\Types\Type;
use Tagcade\Domain\DTO\Report\UnifiedReport\AdTagCountry as AdTagCountryDTO;
use Tagcade\Domain\DTO\Report\UnifiedReport\AdTagGroupCountry as AdTagGroupCountryDTO;
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

    public function getAdTagCountryReportFor(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->andWhere($qb->expr()->between('r.day', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->andWhere('r.publisherId = :publisherId')
            ->setParameter('publisherId', $publisher->getId())
            ->addSelect('r.id')
            ->addSelect('r.publisherId')
            ->addSelect('r.day')
            ->addSelect('r.tagId')
            ->addSelect('r.adTagName')
            ->addSelect('r.adTagGroupId')
            ->addSelect('r.adTagGroupName')
            ->addSelect('r.country')
            ->addSelect('r.countryName')
            ->addSelect('SUM(r.paidImpressions) as paidImpressions')
            ->addSelect('SUM(r.allImpressions) as allImpressions')
            ->addSelect('SUM(r.pubPayout) as pubPayout')
            ->addSelect('(SUM(r.fillRate * r.paidImpressions) / SUM(r.paidImpressions)) as fillRate')
            ->addSelect('(SUM(r.cpm * r.pubPayout) / SUM(r.pubPayout)) as cpm')
            ->addGroupBy('r.tagId, r.country')
            ->getQuery()
            ->getResult();

        // TODO: get result as array of CountryDaily objects, not mixed array ([Original CountryDaily, id, publisherId, ...]
        if (is_array($result)) {
            $result = array_map(function ($rst) {
                return (is_array($rst) && count($rst) > 14)
                    ? (new AdTagCountryDTO())
                        ->setId($rst['id'])
                        ->setPublisherId($rst['publisherId'])
                        ->setTagId($rst['tagId'])
                        ->setAdTagName($rst['adTagName'])
                        ->setAdTagGroupId($rst['adTagGroupId'])
                        ->setAdTagGroupName($rst['adTagGroupName'])
                        ->setCountry($rst['country'])
                        ->setCountryName($rst['countryName'])
                        ->setPaidImps($rst['paidImpressions'])
                        ->setTotalImps($rst['allImpressions'])
                        ->setPubPayout($rst['pubPayout'])
                        ->setFillRate(is_numeric($rst['fillRate']) ? round($rst['fillRate'], 4) : null)
                        ->setCpm(is_numeric($rst['cpm']) ? round($rst['cpm'], 4) : null)
                    : null;
            }, $result);
        }

        return $result;
    }

    /**
     * get AdTag Group Report For a Publisher by a Country in a date range
     * @param PublisherInterface $publisher
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getAdTagGroupCountryReportFor(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->andWhere($qb->expr()->between('r.day', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->andWhere('r.publisherId = :publisherId')
            ->setParameter('publisherId', $publisher->getId())
            ->addSelect('r.id')
            ->addSelect('r.publisherId')
            ->addSelect('r.day')
            ->addSelect('r.tagId')
            ->addSelect('r.adTagName')
            ->addSelect('r.adTagGroupId')
            ->addSelect('r.adTagGroupName')
            ->addSelect('r.country')
            ->addSelect('r.countryName')
            ->addSelect('SUM(r.paidImpressions) as paidImpressions')
            ->addSelect('SUM(r.allImpressions) as allImpressions')
            ->addSelect('SUM(r.pubPayout) as pubPayout')
            ->addSelect('(SUM(r.fillRate * r.paidImpressions) / SUM(r.paidImpressions)) as fillRate')
            ->addSelect('(SUM(r.cpm * r.pubPayout) / SUM(r.pubPayout)) as cpm')
            ->addGroupBy('r.adTagGroupId, r.country')
            ->getQuery()
            ->getResult();

        // TODO: get result as array of CountryDaily objects, not mixed array ([Original CountryDaily, id, publisherId, ...]
        if (is_array($result)) {
            $result = array_map(function ($rst) {
                return (is_array($rst) && count($rst) > 14)
                    ? (new AdTagGroupCountryDTO())
                        ->setId($rst['id'])
                        ->setPublisherId($rst['publisherId'])
                        ->setAdTagGroupId($rst['adTagGroupId'])
                        ->setAdTagGroupName($rst['adTagGroupName'])
                        ->setCountry($rst['country'])
                        ->setCountryName($rst['countryName'])
                        ->setPaidImps($rst['paidImpressions'])
                        ->setTotalImps($rst['allImpressions'])
                        ->setPubPayout($rst['pubPayout'])
                        ->setFillRate(is_numeric($rst['fillRate']) ? round($rst['fillRate'], 4) : null)
                        ->setCpm(is_numeric($rst['cpm']) ? round($rst['cpm'], 4) : null)
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
                    case self::COUNTRY_DAILY_AD_TAG_NAME_FIELD:
                        $qb->andWhere('r.adTagName LIKE :ad_tag_name')->setParameter('ad_tag_name', '%'.$searchKey.'%');
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_NAME_FIELD:
                        $qb->andWhere('r.adTagGroupName LIKE :ad_tag_group_name')->setParameter('ad_tag_group_name', '%'.$searchKey.'%');
                        break;
                    case self::COUNTRY_DAILY_TAG_ID_FIELD:
                        $qb->andWhere('r.tagId = :tag_id')->setParameter('tag_id', intval($searchKey), Type::INTEGER);
                        break;
                    case self::COUNTRY_DAILY_AD_TAG_GROUP_ID_FIELD:
                        $qb->andWhere('r.adTagGroupId = :ad_tag_group_id')->setParameter('ad_tag_group_id', intval($searchKey), Type::INTEGER);
                        break;
                    case self::COUNTRY_DAILY_PUBLISHER_FIELD:
                        $qb->andWhere('r.publisherId = :publisher_id')
                            ->setParameter('publisher_id', intval($searchKey), Type::INTEGER);
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_FIELD:
                        $qb->andWhere('r.country LIKE :country')->setParameter('country', '%'.$searchKey.'%');
                        break;
                    case self::COUNTRY_DAILY_COUNTRY_NAME_FIELD:
                        $qb->andWhere('r.countryName LIKE :country_name')->setParameter('country_name', '%'.$searchKey.'%');
                        break;
                }
            }
        }

        if ($sortField !== null && $sortDirection !== null && in_array($sortDirection, [self::SORT_DIRECTION_ASC, self::SORT_DIRECTION_DESC])) {
            switch ($sortField) {
                case self::COUNTRY_DAILY_FILL_RATE_FIELD:
                    $qb->addOrderBy('r.fillRate', $sortDirection);
                    break;
                case self::COUNTRY_DAILY_PAID_IMPS_FIELD:
                    $qb->addOrderBy('r.paidImps', $sortDirection);
                    break;
                case self::COUNTRY_DAILY_ALL_IMPS_FIELD:
                    $qb->addOrderBy('r.allImpressions', $sortDirection);
                    break;
                case self::COUNTRY_DAILY_PUB_PAYOUT_FIELD:
                    $qb->addOrderBy('r.pubPayout', $sortDirection);
                    break;
                case self::COUNTRY_DAILY_CPM_FIELD:
                    $qb->addOrderBy('r.cpm', $sortDirection);
                    break;
            }
        }
        else {
            $qb->addOrderBy('r.id', 'asc');
        }

        return $qb->getQuery();
    }
}