<?php


namespace Tagcade\Repository\Pager\UnifiedReport\PulsePoint;


use Doctrine\DBAL\Types\Type;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Report\UnifiedReport\PulsePoint\AdTagDomainImpression;
use Tagcade\Entity\Report\UnifiedReport\PulsePoint\AdTagDomainImpression as AdTagDomainImpressionEntity;

class AdTagDomainImpressionRepository extends EntityRepository implements AdTagDomainImpressionRepositoryInterface, PaginatorAwareInterface
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
    const SORT_DIRECTION = ["asc", "desc"];

    /**
     * @var PaginatorInterface
     */
    protected $paginator;
    /**
     * Sets the KnpPaginator instance.
     *
     * @param Paginator $paginator
     *
     * @return mixed
     */
    public function setPaginator(Paginator $paginator)
    {
        // TODO: Implement setPaginator() method.
        $this->paginator = $paginator;
    }

    public function getReportFor(PublisherInterface $publisher, UnifiedReportParams $params)
    {
        $qb = $this->getReportsInRange($params->getStartDate(), $params->getEndDate(), $params->getSearchField(), $params->getSearchKey(), $params->getSortField(), $params->getSortDirection());
        $qb
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
        ;

        /**
         * @var SlidingPagination $paginations
         */
        $paginations =  $this->paginator->paginate(
            $this->getReportsInRange($params->getStartDate(), $params->getEndDate(), $params->getSearchField(), $params->getSearchKey(), $params->getSortField(), $params->getSortDirection()),
            $params->getPage(),
            $params->getSize()
        );

        $result = $paginations->getItems();
        // TODO: get result as array of DailyCountry objects, not mixed array ([Original AdTagDomainImpression, id, publisherId, ...]
        if (is_array($result)) {
            $result = array_map(function (AdTagDomainImpressionEntity $rst) {
                    (new AdTagDomainImpression())
                        ->setId($rst->getId())
                        ->setPublisherId($rst->getPublisherId())
                        ->setDomain($rst->getDomain())
                        ->setTotalImps($rst->getTotalImps())
                        ->setPaidImps($rst->getPaidImps())
                        ->setFillRate($rst->getFillRate())
                        ->setDomainStatus($rst->getDomainStatus())
                        ->setAdTag($rst->getAdTag())
                        ->setAdTagId($rst->getAdTagId())
                        ->setDate($rst->getDate())
                    ;
            }, $result);
        }

        return $result;
    }

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param null $searchField
     * @param null $searchKey
     * @param null $sortField
     * @param null $sortDirection
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getReportsInRange(\DateTime $startDate, \DateTime $endDate, $searchField = null, $searchKey = null, $sortField = null, $sortDirection = null)
    {
        $qb = $this->createQueryBuilder('r');

        $qb
            ->andWhere($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
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

        if ($sortField !== null && $sortDirection !== null && in_array($sortDirection, self::SORT_DIRECTION)) {
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
    }
}