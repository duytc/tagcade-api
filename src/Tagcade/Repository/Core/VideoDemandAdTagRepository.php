<?php


namespace Tagcade\Repository\Core;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Core\WaterfallPlacementRuleInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class VideoDemandAdTagRepository extends EntityRepository implements VideoDemandAdTagRepositoryInterface
{
    protected $SORT_FIELDS = [
        'id' => 'id',
        'timeout' => 'timeout',
        'sellPrice' => 'sellPrice',
        'active' => 'active'
    ];
    /**
     * @inheritdoc
     */
    public function getAll($limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('vdm')
            ->leftJoin('vdm.libraryVideoDemandAdTag', 'vdp')
            ->andWhere('vdm.deletedAt IS NULL')
            ->andWhere('vdm.videoWaterfallTagItem IS NOT NULL');

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getVideoDemandAdTagsForPublisherWithPagination(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('vdm')
            ->leftJoin('vdm.libraryVideoDemandAdTag', 'libraryVideoDemandAdTag')
            ->leftJoin('libraryVideoDemandAdTag.videoDemandPartner', 'videoDemandPartner')
            ->andWhere('vdm.deletedAt IS NULL')
            ->andWhere('vdm.videoWaterfallTagItem IS NOT NULL');

        if ($user instanceof PublisherInterface) {
            $qb
                ->where('videoDemandPartner.publisher = :publisher_id')
                ->setParameter('publisher_id', $user->getId(), Type::INTEGER);
        }

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like('vdm.id', ':searchKey'),
                    $qb->expr()->like('vdm.priority', ':searchKey'),
                    $qb->expr()->like('vdm.rotation_weight', ':searchKey'),
                    $qb->expr()->like('libraryVideoDemandAdTag.name', ':searchKey'),
                    $qb->expr()->like('libraryVideoDemandAdTag.timeout', ':searchKey'),
                    $qb->expr()->like('libraryVideoDemandAdTag.sellPrice', ':searchKey')
                ))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            switch ($param->getSortField()){
                case 'active':
                    $qb->addOrderBy('vdm'.$param->getSortField(), $param->getSortDirection());
                    break;
                case 'name':
                    $qb->addOrderBy('libraryVideoDemandAdTag'.$param->getSortField(), $param->getSortDirection());
                    break;
                case 'timeout':
                    $qb->addOrderBy('libraryVideoDemandAdTag'.$param->getSortField(), $param->getSortDirection());
                    break;
                case 'sellPrice':
                    $qb->addOrderBy('libraryVideoDemandAdTag'.$param->getSortField(), $param->getSortDirection());
                    break;
                case 'priority':
                    $qb->addOrderBy('vdm.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case 'rotation_weight' :
                    $qb->addOrderBy('vdm.' . $param->getSortField(), $param->getSortDirection());
                    break;
                default:
                    break;
            }
        }

        return $qb;
    }

    /**
     * @inheritdoc
     */
    public function getVideoDemandAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('vdm')
            ->leftJoin('vdm.libraryVideoDemandAdTag', 'libraryVideoDemandAdTag')
            ->leftJoin('libraryVideoDemandAdTag.videoDemandPartner', 'videoDemandPartner')
            ->where('videoDemandPartner.publisher = :publisher_id')
            ->andWhere('vdm.deletedAt IS NULL')
            ->andWhere('vdm.videoWaterfallTagItem IS NOT NULL')
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
    public function getVideoDemandAdTagsForVideoWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('vdm')
            ->leftJoin('vdm.videoWaterfallTagItem', 'i')
            ->where('i.videoWaterfallTag = :video_waterfall_tag')
            ->setParameter('video_waterfall_tag', $videoWaterfallTag);

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
    public function getVideoDemandAdTagsForDemandPartner(VideoDemandPartnerInterface $videoDemandPartner, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('vdt')
            ->leftJoin('vdt.libraryVideoDemandAdTag', 'lvdt')
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
    public function getVideoDemandAdTagsNotBelongToVideoTagItem(UserRoleInterface $user, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('vdm')
            ->andWhere('vdm.videoWaterfallTagItem IS NULL');

        if ($user instanceof PublisherInterface) {
            $qb->leftJoin('vdm.videoDemandPartner', 'vdp')
                ->andWhere('vdp.publisher = :publisher_id')
                ->setParameter('publisher_id', $user->getId(), Type::INTEGER);
        }

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
    public function getVideoDemandAdTagsByFilterParams(FilterParameterInterface $filterParameter)
    {
        $qb = $this->createQueryBuilder('videoDemandAdTag')
            ->leftJoin('videoDemandAdTag.videoWaterfallTagItem', 'videoWaterfallTagItem')
            ->leftJoin('videoWaterfallTagItem.videoWaterfallTag', 'videoWaterfallTag')
            ->leftJoin('videoWaterfallTag.videoPublisher', 'videoPublisher')
            ->leftJoin('videoPublisher.publisher', 'publisher')
            ->leftJoin('videoDemandAdTag.libraryVideoDemandAdTag', 'libraryVideoDemandAdTag')
            ->leftJoin('libraryVideoDemandAdTag.videoDemandPartner', 'videoDemandPartner');

        if ($filterParameter->getPublishers() != null) {
            $qb->andWhere($qb->expr()->in('publisher.id', $filterParameter->getPublishers()));
        }

        if ($filterParameter->getVideoPublishers() != null) {
            $qb->andWhere($qb->expr()->in('videoPublisher.id', $filterParameter->getVideoPublishers()));
        }

        // we get all demandAdTags by either publishers or demandPartners or VideoWaterfallTags or VideoDemandAdTags
        if ($filterParameter->getVideoDemandPartners() != null) {
            $qb->andWhere($qb->expr()->in('videoDemandPartner.id', $filterParameter->getVideoDemandPartners()));
        }

        if ($filterParameter->getVideoWaterfallTags() != null) {
            $qb->andWhere($qb->expr()->in('videoWaterfallTag.id', $filterParameter->getVideoWaterfallTags()));
        }

        if ($filterParameter->getVideoDemandAdTags() != null) {
            $qb->andWhere($qb->expr()->in('videoDemandAdTag.id', $filterParameter->getVideoDemandAdTags()));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getVideoDemandAdTagsForLibraryVideoDemandAdTag(LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('vdt')
            ->where('vdt.libraryVideoDemandAdTag = :libraryVideoDemandAdTag')
            ->setParameter('libraryVideoDemandAdTag', $libraryVideoDemandAdTag);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param LibraryVideoDemandAdTagInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getVideoDemandAdTagsForLibraryVideoDemandAdTagWithPagination(LibraryVideoDemandAdTagInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('vdt')
            ->where('vdt.libraryVideoDemandAdTag = :libraryVideoDemandAdTag')
            ->setParameter('libraryVideoDemandAdTag', $user);

        return $qb;
    }

    public function getVideoDemandAdTagsForWaterfallPlacementRule(WaterfallPlacementRuleInterface $rule, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('vdt')
            ->where('vdt.waterfallPlacementRule = :rule')
            ->setParameter('rule', $rule);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function getVideoDemandAdTagsHaveRequestCapByStatus($status)
    {
        return $this->createQueryBuilder('vdt')
            ->where('vdt.requestCap IS NOT NULL OR vdt.impressionCap IS NOT NULL')
            ->andWhere('vdt.active = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getVideoDemandAdTagsByStatus($status)
    {
        return $this->createQueryBuilder('vdt')
            ->where('vdt.active = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult()
            ;
    }
}