<?php


namespace Tagcade\Repository\Core;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class LibraryVideoDemandAdTagRepository extends EntityRepository implements LibraryVideoDemandAdTagRepositoryInterface
{
    protected $SORT_FIELDS = [
        'id' => 'id',
        'name' => 'name',
        'videoDemandPartner.publisher.company' => 'videoDemandPartner.publisher.company',
        'timeout' => 'timeout',
        'sellPrice' => 'sellPrice',
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
     * @param VideoDemandPartnerInterface $user
     * @param Request $request
     * @return mixed
     */
    public function getLibraryVideoDemandAdTagsForDemandPartnerWithPagination(VideoDemandPartnerInterface $user, Request $request)
    {
        $qb = $this->createQueryBuilder('lvdt');
        if ($user instanceof PublisherInterface) {
            $qb
                ->where('lvdt.videoDemandPartner = :videoDemandPartner')
                ->setParameter('videoDemandPartner', $user);
        }

        $searchKey = $request->query->get('searchKey');
        if (is_string($searchKey)) {
            $searchLike = sprintf('%%%s%%', $searchKey);
            $qb
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like('lvdt.id', ':searchKey'),
                    $qb->expr()->like('lvdt.name', ':searchKey')
                ))
                ->setParameter('searchKey', $searchLike);
        }

        $sortField = $request->query->get('sortField');
        $sortDirection = $request->query->get('orderBy');

        if (is_string($sortField) &&
            is_string($sortDirection) &&
            in_array($sortDirection, ['asc', 'desc', 'ASC', 'DESC'])&&
            in_array($sortField, $this->SORT_FIELDS)
        ) {
            $qb->addOrderBy('lvdt.' . $sortField, $sortDirection);
        }

        return $qb;
    }

    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getLibraryVideoDemandAdTagsForPublisherWithPagination(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('r')
            ->join('r.videoDemandPartner', 'vdp');

        if ($user instanceof PublisherInterface) {
            $qb
                ->where('vdp.publisher = :publisher')
                ->setParameter('publisher', $user);
        }
        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like('r.id', ':searchKey'),
                    $qb->expr()->like('r.name', ':searchKey'),
                    $qb->expr()->like('r.company', ':searchKey')
                ))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            $qb->addOrderBy('vdp.' . $param->getSortField(), $param->getSortDirection());
        }

        return $qb;
    }
}