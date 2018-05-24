<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

interface VideoWaterfallTagManagerInterface extends ManagerInterface
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
     * @return array|\Tagcade\Model\Core\VideoWaterfallTagInterface[]
     */
    public function getVideoWaterfallTagsForVideoPublisher(VideoPublisherInterface $videoPublisher, $autoOptimize = null, $limit = null, $offset = null);

    /**
     * @param LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag
     * @param UserRoleInterface $user
     * @return mixed
     */
    public function getWaterfallTagsNotLinkToLibraryVideoDemandAdTag(LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag, UserRoleInterface $user);

    /**
     * @param VideoDemandPartnerInterface $demandPartner
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getWaterfallTagsForVideoDemandPartner(VideoDemandPartnerInterface $demandPartner, $limit = null, $offset = null);

    /**
     * @param $name
     * @param VideoPublisherInterface $videoPublisher
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function findByNameAndVideoPublisher($name, VideoPublisherInterface $videoPublisher, $limit = null, $offset = null);
}