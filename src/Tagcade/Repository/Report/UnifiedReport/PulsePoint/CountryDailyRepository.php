<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;


use Doctrine\DBAL\Types\Type;
use Tagcade\Domain\DTO\Report\UnifiedReport\AdTagCountry as AdTagCountryDTO;
use Tagcade\Domain\DTO\Report\UnifiedReport\AdTagGroupCountry as AdTagGroupCountryDTO;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;

class CountryDailyRepository extends AbstractReportRepository implements CountryDailyRepositoryInterface
{
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
}