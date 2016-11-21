<?php


namespace Tagcade\Repository\Core;


use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\WaterfallPlacementRuleInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

interface VideoDemandAdTagRepositoryInterface extends ObjectRepository
{
    /**
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getAll($limit = null, $offset = null);

    /**
     * get all VideoDemandAdTags For a Publisher
     *
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getVideoDemandAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getVideoDemandAdTagsForVideoWaterfallTag(VideoWaterfallTagInterface $videoWaterfallTag, $limit = null, $offset = null);

    /**
     * @param VideoDemandPartnerInterface $videoDemandPartner
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getVideoDemandAdTagsForDemandPartner(VideoDemandPartnerInterface $videoDemandPartner, $limit = null, $offset = null);

    /**
     * @param UserRoleInterface $user
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getVideoDemandAdTagsNotBelongToVideoTagItem(UserRoleInterface $user, $limit = null, $offset = null);

    /**
     * get VideoDemandAdTags By FilterParams
     *
     * @param FilterParameterInterface $filterParameter
     * @return array
     */
    public function getVideoDemandAdTagsByFilterParams(FilterParameterInterface $filterParameter);

    /**
     * @param LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag
     * @param null|int $limit
     * @param null|int $offset
     * @return array|LibraryVideoDemandAdTagInterface[]
     */
    public function getVideoDemandAdTagsForLibraryVideoDemandAdTag(LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag, $limit = null, $offset = null);

    /**
     * @param WaterfallPlacementRuleInterface $rule
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getVideoDemandAdTagsForWaterfallPlacementRule(WaterfallPlacementRuleInterface $rule, $limit = null, $offset = null);

    /**
     * @param $status
     * @return mixed
     */
    public function getVideoDemandAdTagsHaveRequestCapByStatus($status);

    /**
     * @param $status
     * @return mixed
     */
    public function getVideoDemandAdTagsByStatus($status);
}