<?php


namespace Tagcade\Repository\Core;


use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

interface VideoWaterfallTagRepositoryInterface extends ObjectRepository
{
    /**
     * get all VideoWaterfallTags For a Publisher
     *
     * @param PublisherInterface $publisher
     * @param null|int $limit
     * @param null|int $offset
     * @return array|VideoWaterfallTagInterface[]
     */
    public function getVideoWaterfallTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * get all VideoWaterfallTags For a Video Publisher
     *
     * @param VideoPublisherInterface $videoPublisher
     * @param null|bool $autoOptimize
     * @param null|int $limit
     * @param null|int $offset
     * @return mixed
     */
    public function getVideoWaterfallTagsForVideoPublisher(VideoPublisherInterface $videoPublisher, $autoOptimize = null, $limit = null, $offset = null);

    /**
     * @param VideoPublisherInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getVideoWaterfallTagsForVideoPublisherWithPagination(VideoPublisherInterface $user, PagerParam $param);

    /**
     * Get all video ad tag by filter parameter
     * @param FilterParameterInterface $filterParameter
     * @return mixed
     */
    public function getVideoWaterfallTagsByFilterParams(FilterParameterInterface $filterParameter);

    /**
     * @param LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag
     * @param null|UserRoleInterface $user
     * @return mixed
     */
    public function getWaterfallTagsNotLinkToLibraryVideoDemandAdTag(LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag, $user = null);

    /**
     * @param VideoDemandPartnerInterface $demandPartner
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getWaterfallTagsForVideoDemandPartner(VideoDemandPartnerInterface $demandPartner, $limit = null, $offset = null);

    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @param null|bool $autoOptimize
     * @return QueryBuilder
     */
    public function getWaterfallTagForUserWithPagination(UserRoleInterface $user, PagerParam $param, $autoOptimize = null);

    /**
     * @param PublisherInterface $publisher
     * @param array $videoPublisher
     * @param $price
     * @return mixed
     */
    public function getWaterfallTagHaveBuyPriceLowerThanAndBelongsToListPublishers(PublisherInterface $publisher, array $videoPublisher, $price);

    /**
     * @param $name
     * @param VideoPublisherInterface $videoPublisher
     * @param $limit
     * @param $offset
     * @return mixed
     */
    public function findByNameAndVideoPublisher($name, VideoPublisherInterface $videoPublisher, $limit, $offset);
}