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
use Tagcade\Domain\DTO\Report\UnifiedReport\AdTagGroupDaily as AdTagGroupDailyDTO;
use Tagcade\Entity\Report\UnifiedReport\PulsePoint\AccountManagement as AccountManagementEntity;

class AccountManagementRepository extends EntityRepository implements AccountManagementRepositoryInterface, PaginatorAwareInterface
{
    // search fields
    const ACC_MNG_PUBLISHER_FIELD = "publisherId";
    const ACC_MNG_AD_TAG_GROUP_FIELD = "adTagGroup";
    const ACC_MNG_AD_TAG_ID_FIELD = "adTagId";
    const ACC_MNG_AD_TAG_FIELD = "adTag";
    const ACC_MNG_STATUS_FIELD = "status";
    // sort fields
    const ACC_MNG_REVENUE_FIELD = "revenue";
    const ACC_MNG_FILL_RATE_FIELD = "fillRate";
    const ACC_MNG_PAID_IMPS_FIELD = "paidImps";
    const ACC_MNG_BACKUP_IMPRESSION_FIELD = "backupImpression";
    const ACC_MNG_TOTAL_IMPS_FIELD = "totalImps";
    const ACC_MNG_AVG_CPM_FIELD = "avgCpm";
    const ACC_MNG_DATE_FIELD = "date";
    // sort direction
    const SORT_DIRECTION_ASC = "asc";
    const SORT_DIRECTION_DESC = "desc";

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
        return $this->paginator->paginate(
            $this->getReportsInRange($params->getStartDate(), $params->getEndDate(), $params->getSearchField(), $params->getSearchKey(), $params->getSortField(), $params->getSortDirection()),
            $params->getPage(),
            $params->getSize()
        );
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
                case self::ACC_MNG_AD_TAG_FIELD :
                    $qb->andWhere($qb->expr()->like('r.adTag', $searchKey));
                    break;
                case self::ACC_MNG_AD_TAG_GROUP_FIELD:
                    $qb->andWhere($qb->expr()->like('r.adTagGroup', $searchKey));
                    break;
                case self::ACC_MNG_AD_TAG_ID_FIELD:
                    $qb->andWhere($qb->expr()->like('r.adTagId', $searchKey));
                    break;
                case self::ACC_MNG_PUBLISHER_FIELD:
                    $qb->andWhere('r.publisherId = :publisher_id')
                        ->setParameter('publisher_id', intval($searchKey), Type::INTEGER);
                    break;
                case self::ACC_MNG_STATUS_FIELD:
                    $qb->andWhere('r.status = :status')
                        ->setParameter('status', $searchKey, Type::STRING);
                    break;
            }
        }

        if ($sortField !== null && $sortDirection !== null && in_array($sortDirection, [self::SORT_DIRECTION_ASC, self::SORT_DIRECTION_DESC])) {
            switch ($sortField) {
                case self::ACC_MNG_REVENUE_FIELD :
                    $qb->addOrderBy('r.revenue', $sortDirection);
                    break;
                case self::ACC_MNG_FILL_RATE_FIELD:
                    $qb->addOrderBy('r.fillRate', $sortDirection);
                    break;
                case self::ACC_MNG_PAID_IMPS_FIELD:
                    $qb->addOrderBy('r.paidImps', $sortDirection);
                    break;
                case self::ACC_MNG_BACKUP_IMPRESSION_FIELD:
                    $qb->addOrderBy('r.backupImpression', $sortDirection);
                    break;
                case self::ACC_MNG_TOTAL_IMPS_FIELD:
                    $qb->addOrderBy('r.totalImps', $sortDirection);
                    break;
                case self::ACC_MNG_AVG_CPM_FIELD:
                    $qb->addOrderBy('r.avgCpm', $sortDirection);
                    break;
                case self::ACC_MNG_DATE_FIELD:
                    $qb->addOrderBy('r.date', $sortDirection);
                    break;
            }
        }
    }

    public function getAdTagGroupDailyReportFor(PublisherInterface $publisher, UnifiedReportParams $params)
    {
        $qb = $this->getReportsInRange($params->getStartDate(), $params->getEndDate(), $params->getSearchField(), $params->getSearchKey(), $params->getSortField(), $params->getSortDirection());

        $qb
            ->andWhere('r.publisherId = :publisherId')
            ->setParameter('publisherId', $publisher->getId())
            ->addSelect('r.id')
            ->addSelect('r.publisherId')
            ->addSelect('r.adTagGroup')
            ->addSelect('r.adTag')
            ->addSelect('r.adTagId')
            ->addSelect('r.status')
            ->addSelect('r.size')
            ->addSelect('r.askPrice')
            ->addSelect('SUM(r.revenue) as revenue')
            ->addSelect('(SUM(r.fillRate * r.paidImps) / SUM(r.paidImps)) as fillRate')
            ->addSelect('SUM(r.paidImps) as paidImps')
            ->addSelect('SUM(r.backupImpression) as backupImpression')
            ->addSelect('SUM(r.totalImps) as totalImps')
            ->addSelect('(SUM(r.avgCpm * r.revenue) / SUM(r.revenue)) as avgCpm')
            ->addSelect('r.date')
            ->addGroupBy('r.adTagGroup, r.date')
        ;

        /**
         * @var SlidingPagination $paginations
         */
        $paginations = $this->paginator->paginate($qb, $params->getPage(), $params->getSize());
        $result = $paginations->getItems();
        // TODO: get result as array of DailyCountry objects, not mixed array ([Original AccountManagement, id, publisherId, ...]
        if (is_array($result)) {
            $result = array_map(function (AccountManagementEntity $rst) {
                    (new AdTagGroupDailyDTO())
                        ->setId($rst->getId())
                        ->setPublisherId($rst->getPublisherId())
                        ->setAdTagGroup($rst->getAdTagGroup())
                        ->setRevenue($rst->getRevenue())
                        ->setFillRate($rst->getFillRate())
                        ->setPaidImps($rst->getPaidImps())
                        ->setBackupImpression($rst->getBackupImpression())
                        ->setTotalImps($rst->getTotalImps())
                        ->setAvgCpm($rst->getAvgCpm())
                        ->setDate($rst->getDate())
                    ;
            }, $result);
        }

        return $result;
    }
}