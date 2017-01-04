<?php


namespace Tagcade\Repository\Core;


use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class LibraryVideoDemandAdTagRepository extends EntityRepository implements LibraryVideoDemandAdTagRepositoryInterface
{
    protected $SORT_FIELDS = [
        'id' => 'id',
        'tagURL' => 'tagURL',
        'name' => 'name',
        'timeout' => 'timeout',
        //'targeting' => 'targeting',
        //'sellPrice' => 'sellPrice',
        //'deletedAt' => 'deletedAt',
        'videoDemandPartner' => 'videoDemandPartner',
        //'videoDemandAdTags' => 'videoDemandAdTags',
        //'waterfallPlacementRules' => 'waterfallPlacementRules'
    ];

    /**
     * @inheritdoc
     */
    public function getLibraryVideoDemandAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('r')
            ->join('r.videoDemandPartner', 'vdp')
            ->where('vdp.publisher = :publisher')
            ->setParameter('publisher', $publisher);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getLibraryVideoDemandAdTagsForDemandPartner(VideoDemandPartnerInterface $videoDemandPartner, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('lvdt')
            ->where('lvdt.videoDemandPartner = :videoDemandPartner')
            ->setParameter('videoDemandPartner', $videoDemandPartner);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getLibraryDemandTagsForUserWithPagination(UserRoleInterface $user, PagerParam $param = null)
    {
        $qb = $this->createQueryBuilder('r')
            ->join('r.videoDemandPartner', 'vdp');

        if ($user instanceof PublisherInterface) {
            $qb
                ->where('vdp.publisher = :publisher')
                ->setParameter('publisher', $user->getId());
        }

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like('r.id', ':searchKey'),
                    $qb->expr()->like('r.tagURL', ':searchKey'),
                    $qb->expr()->like('r.name', ':searchKey'),
                    $qb->expr()->like('r.timeout', ':searchKey'),
                    $qb->expr()->like('vdp.name', ':searchKey')
                ))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            switch ($param->getSortField()) {
                case $this->SORT_FIELDS['id']:
                case $this->SORT_FIELDS['tagURL']:
                case $this->SORT_FIELDS['name']:
                case $this->SORT_FIELDS['timeout']:
                    $qb->addOrderBy('r.' . $param->getSortField(), $param->getSortDirection());
                    break;

                case $this->SORT_FIELDS['videoDemandPartner']:
                    $qb->addOrderBy('vdp.' . 'name', $param->getSortDirection());
                    break;

                default:
                    break;
            }
        }

        return $qb;
    }
}