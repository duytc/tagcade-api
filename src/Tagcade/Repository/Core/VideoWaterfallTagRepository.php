<?php


namespace Tagcade\Repository\Core;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class VideoWaterfallTagRepository extends EntityRepository implements VideoWaterfallTagRepositoryInterface
{
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
}