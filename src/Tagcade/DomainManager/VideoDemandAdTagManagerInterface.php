<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

interface VideoDemandAdTagManagerInterface extends ManagerInterface
{
    /**
     * get all VideoDemandAdTags For a Publisher
     *
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return array|VideoDemandAdTagInterface[]
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
     * @param LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag
     * @param null|int $limit
     * @param null|int $offset
     * @return array|LibraryVideoDemandAdTagInterface[]
     */
    public function getVideoDemandAdTagsForLibraryVideoDemandAdTag(LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag, $limit = null, $offset = null);

    /**
     * @param VideoDemandPartnerInterface $demandPartner
     * @param bool $active
     * @param null $waterfall
     * @return mixed
     */
    public function updateVideoDemandAdTagForDemandPartner(VideoDemandPartnerInterface $demandPartner, $active = false, $waterfall = null);

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

    /**
     * @param $name
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function findByName($name, $limit = null, $offset = null);

    /**
     * @param $demandPartner
     * @param $waterfall
     * @param $tagName
     * @return mixed
     */
    public function findByDemandPartnerWaterfallAndTagName($demandPartner, $waterfall, $tagName);
}