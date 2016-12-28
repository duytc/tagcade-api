<?php


namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Entity\Core\VideoDemandPartner;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class VideoDemandPartnerRepository extends EntityRepository implements VideoDemandPartnerRepositoryInterface
{
    protected $SORT_FIELDS = [
        'id' => 'id',
        'name' => 'name',
        'publisher.company' => 'company',
        'activeAdTagsCount' => 'active_ad_tags_count',
        'pausedAdTagsCount' => 'paused_ad_tags_count',
    ];

    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getVideoDemandPartnersForPublisherWithPagination(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('vdm');
        if ($user instanceof PublisherInterface) {
            $qb
                ->where('vdm.publisher = :publisher_id')
                ->setParameter('publisher_id', $user->getId(), Type::INTEGER);
        }

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like('vdm.id', ':searchKey'),
                    $qb->expr()->like('vdm.name', ':searchKey'),
                    $qb->expr()->like('vdm.company', ':searchKey')
                ))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            $qb->addOrderBy('vdm.' . $param->getSortField(), $param->getSortDirection());
        }

        return $qb;
    }
    /**
     * @inheritdoc
     */
    public function getVideoDemandPartnersForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('vdm')
            ->where('vdm.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER);

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
    public function getVideoDemandPartnerForPublisherByCanonicalName(PublisherInterface $publisher, $canonicalName)
    {
        $qb = $this->createQueryBuilder('vdm')
            ->where('vdm.publisher = :publisher_id')
            ->andWhere('vdm.nameCanonical = :canonicalName')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER)
            ->setParameter('canonicalName', $canonicalName);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getVideoDemandPartnersByFilterParams(FilterParameterInterface $filterParameter)
    {
        $qb = $this->createQueryBuilder('vdm');

        if ($filterParameter->getPublishers() != null) {
            $qb
                ->andWhere($qb->expr()->in('vdm.publisher', $filterParameter->getPublishers()));
        }

        // we get all videoDemandPartners by either publishers or demandPartners
        if ($filterParameter->getVideoDemandPartners() != null) {
            $qb
                ->andWhere($qb->expr()->in('vdm.id', $filterParameter->getVideoDemandPartners()));
        }

        return $qb->getQuery()->getResult();
    }
}