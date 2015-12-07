<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;


use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Report\UnifiedReport\PulsePoint\AdTagDomainImpression;
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
    // sort direction
    const SORT_DIRECTION_ASC = "asc";
    const SORT_DIRECTION_DESC = "desc";


    public function getReportFor(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate)
    {
        $qb = parent::getReportsInRange($startDate, $endDate);

        $result = $qb
            ->andWhere('r.publisherId = :publisherId')
            ->setParameter('publisherId', $publisher->getId())
            ->addSelect('r.id')
            ->addSelect('r.publisherId')
            ->addSelect('r.domain')
            ->addSelect('SUM(r.totalImps) as totalImps')
            ->addSelect('SUM(r.paidImps) as paidImps')
            ->addSelect('(SUM(r.fillRate * r.paidImps) / SUM(r.paidImps)) as fillRate')
            ->addSelect('r.domainStatus')
            ->addSelect('r.adTag')
            ->addSelect('r.adTagId')
            ->addSelect('r.date')
            ->addGroupBy('r.adTagId, r.domain')
            ->getQuery()
            ->getResult();

        // TODO: get result as array of DailyCountry objects, not mixed array ([Original AdTagDomainImpression, id, publisherId, ...]
        if (is_array($result)) {
            $result = array_map(function ($rst) {
                return (is_array($rst) && count($rst) > 10)
                    ? (new AdTagDomainImpression())
                        ->setId($rst['id'])
                        ->setPublisherId($rst['publisherId'])
                        ->setDomain($rst['domain'])
                        ->setTotalImps($rst['totalImps'])
                        ->setPaidImps($rst['paidImps'])
                        ->setFillRate(is_numeric($rst['fillRate']) ? round($rst['fillRate'], 4) : null)
                        ->setDomainStatus($rst['domainStatus'])
                        ->setAdTag($rst['adTag'])
                        ->setAdTagId($rst['adTagId'])
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

        if ($searchField !== null && $searchKey !== null) {
            switch ($searchField) {
                case self::AD_TAG_DOMAIN_IMP_AD_TAG_FIELD :
                    $qb->andWhere($qb->expr()->like('r.adTag', $searchKey));
                    break;
                case self::AD_TAG_DOMAIN_IMP_DOMAIN_FIELD:
                    $qb->andWhere($qb->expr()->like('r.domain', $searchKey));
                    break;
                case self::AD_TAG_DOMAIN_IMP_AD_TAG_ID_FIELD:
                    $qb->andWhere($qb->expr()->like('r.adTagId', $searchKey));
                    break;
                case self::AD_TAG_DOMAIN_IMP_PUBLISHER_FIELD:
                    $qb->andWhere('r.publisherId = :publisher_id')
                        ->setParameter('publisher_id', intval($searchKey), Type::INTEGER);
                    break;
                case self::AD_TAG_DOMAIN_IMP_DOMAIN_STATUS_FIELD:
                    $qb->andWhere('r.domainStatus = :status')
                        ->setParameter('status', $searchKey, Type::STRING);
                    break;
            }
        }

        if ($sortField !== null && $sortDirection !== null && in_array($sortDirection, [self::SORT_DIRECTION_ASC, self::SORT_DIRECTION_DESC])) {
            switch ($sortField) {
                case self::AD_TAG_DOMAIN_IMP_FILL_RATE_FIELD:
                    $qb->addOrderBy('r.fillRate', $sortDirection);
                    break;
                case self::AD_TAG_DOMAIN_IMP_PAID_IMPS_FIELD:
                    $qb->addOrderBy('r.paidImps', $sortDirection);
                    break;
                case self::AD_TAG_DOMAIN_IMP_TOTAL_IMPS_FIELD:
                    $qb->addOrderBy('r.totalImps', $sortDirection);
                    break;
                case self::AD_TAG_DOMAIN_IMP_DATE_FIELD:
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