<?php


namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Tagcade\Entity\Core\VideoPublisher;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class VideoWaterfallTagRepository extends EntityRepository implements VideoWaterfallTagRepositoryInterface
{
    protected $SORT_FIELDS = [
        'id'=>'id',
        'name'=>'name',
        'timeout'=>'timeout',
        'sellPrice'=>'sellPrice',
        'buyPrice' => 'buyPrice',
//        'videoPublisher.name'=>'videoPublisher.name'
    ];

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('vwt')
            ->leftJoin('vwt.videoPublisher', 'vp')
            ->where('vp.publisher = :publisher_id')
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
     * create QueryBuilder For Publisher
     * @param PublisherInterface $publisher
     * @return QueryBuilder qb with alias 'st'
     */
    public function createQueryBuilderForPublisher(PublisherInterface $publisher)
    {
        $qb = $this->createQueryBuilder('wt')
            ->leftJoin('wt.videoPublisher', 'vp')
            ->where('vp.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER);


        return $qb;
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagsForVideoPublisher(VideoPublisherInterface $videoPublisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('vwt')
            ->where('vwt.videoPublisher = :videoPublisher')
            ->setParameter('videoPublisher', $videoPublisher->getId(), Type::INTEGER);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }
    /**
     * @param VideoPublisherInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getVideoWaterfallTagsForVideoPublisherWithPagination(VideoPublisherInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('vwt')
            ->leftJoin('vwt.videoPublisher', 'vp');

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like('vwt.name', ':searchKey'),
                    $qb->expr()->like('vwt.id', ':searchKey')
                ))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            $qb->addOrderBy('vwt.' . $param->getSortField(), $param->getSortDirection());
        }

        return $qb;
    }



    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagsByFilterParams(FilterParameterInterface $filterParameter)
    {
        $qb = $this->createQueryBuilder('wft')
            ->leftJoin('wft.videoWaterfallTagItems','item')
            ->leftJoin('item.videoDemandAdTags','videoDemandAdTags')
            ->leftJoin('videoDemandAdTags.libraryVideoDemandAdTag','library_demandPartner')
            ->leftJoin('library_demandPartner.videoDemandPartner','demandPartner')
            ->leftJoin('demandPartner.publisher', 'pub')
            ->leftJoin('wft.videoPublisher','videoPublisher');

        if (!empty($filterParameter->getPublishers())) {
            $qb ->andWhere($qb->expr()->in('pub.id', $filterParameter->getPublishers()));
        }

        if (!empty($filterParameter->getVideoPublishers())) {
            $qb ->andWhere($qb->expr()->in('videoPublisher.id', $filterParameter->getVideoPublishers()));
        }

        if (!empty($filterParameter->getVideoDemandPartners())) {
            $qb ->andWhere($qb->expr()->in('demandPartner.id', $filterParameter->getVideoDemandPartners()));
        }

        if (!empty($filterParameter->getVideoWaterfallTags())) {
            $qb ->andWhere($qb->expr()->in('wft.id', $filterParameter->getVideoWaterfallTags()));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getWaterfallTagsNotLinkToLibraryVideoDemandAdTag(LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag, $user = null)
    {
        // sub query for selecting linked videoWaterfallTags
        $qbSub = $this->createQueryBuilder('wft')
            ->select('wft.id')
            ->leftJoin('wft.videoWaterfallTagItems', 'wftItem')
            ->leftJoin('wftItem.videoDemandAdTags', 'vdt')
            ->where('vdt.libraryVideoDemandAdTag = :libraryVideoDemandAdTag'); // not set params here

        if ($user instanceof PublisherInterface && !($user instanceof SubPublisherInterface)) {
            $qbSub
                ->leftJoin('wft.videoPublisher', 'pub')
                ->andWhere('pub.publisher = :publisher'); // not set params here
        }

        // main query for selecting not linked videoWaterfallTags
        $qb = $this->createQueryBuilder('r');
        $qb
            ->where($qb->expr()->notIn('r.id', $qbSub->getDQL()))
            ->setParameter('libraryVideoDemandAdTag', $libraryVideoDemandAdTag); // notice: must set parameter for subQuery here, not in subQuery

        if ($user instanceof PublisherInterface && !($user instanceof SubPublisherInterface)) {
            $qb
                ->setParameter('publisher', $user);
        }

        return $qb->getQuery()->getResult();
    }

    public function getWaterfallTagsForVideoDemandPartner(VideoDemandPartnerInterface $demandPartner, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('wft')
            ->leftJoin('wft.videoWaterfallTagItems', 'wftItem')
            ->leftJoin('wftItem.videoDemandAdTags', 'vdt')
            ->leftJoin('vdt.libraryVideoDemandAdTag', 'lib')
            ->where('lib.videoDemandPartner = :demand_partner')
            ->setParameter('demand_partner', $demandPartner);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * create QueryBuilder For User due to Admin or Publisher|SubPublisher
     * @param UserRoleInterface $user
     * @return QueryBuilder qb with alias 'wt'
     */
    private function createQueryBuilderForUser(UserRoleInterface $user)
    {
        return $user instanceof PublisherInterface ? $this->createQueryBuilderForPublisher($user) : $this->createQueryBuilder('wt');
    }

    /**
     * @inheritdoc
     */
    public function getWaterfallTagForUserWithPagination(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilderForUser($user)
            ->leftJoin('wt.videoPublisher', 'vp');

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb->andWhere($qb->expr()->orX($qb->expr()->like('wt.name', ':searchKey'), $qb->expr()->like('wt.id', ':searchKey')))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            switch ($param->getSortField()){
                case 'videoPublisher.name':
                    $qb->addOrderBy('vp.name', $param->getSortDirection());
                    break;
                default :
                    $qb->addOrderBy('wt.' . $param->getSortField(), $param->getSortDirection());
                    break;
            }
        }

        return $qb;
    }

    public function getWaterfallTagHaveBuyPriceLowerThanAndBelongsToListPublishers(PublisherInterface $publisher, array $videoPublisher, $price)
    {
        if ($price === null) {
            return [];
        }

        $qb = $this->createQueryBuilder('r')
            ->select('r.id')
            ->where('r.buyPrice <= :price')
            ->andWhere('r.buyPrice IS NOT NULL')
            ->setParameter('price', $price);

        if (!empty($videoPublisher)) {
            $qb->join('r.videoPublisher', 'p')
            ->andWhere($qb->expr()->in('p.id', $videoPublisher));
        } else {
            $qb->join('r.videoPublisher', 'p')
                ->andWhere('p.publisher = :publisher')
                ->setParameter('publisher', $publisher)
            ;
        }

        $result = $qb->getQuery()->getArrayResult();
        $waterfallTags = [];
        foreach($result as $item) {
            if (is_array($item)) {
                $waterfallTags[] = reset($item);
            } else {
                $waterfallTags[] = $item;
            }
        }

        return $waterfallTags;
    }
}