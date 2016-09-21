<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryVideoDemandAdTagManagerInterface extends ManagerInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param null|int $limit
     * @param null|int $offset
     * @return array|LibraryVideoDemandAdTagInterface[]
     */
    public function getLibraryVideoDemandAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param VideoDemandPartnerInterface $videoDemandPartner
     * @param null|int $limit
     * @param null|int $offset
     * @return array|LibraryVideoDemandAdTagInterface[]
     */
    public function getLibraryVideoDemandAdTagsForDemandPartner(VideoDemandPartnerInterface $videoDemandPartner, $limit = null, $offset = null);

    /**
     * generate VideoDemandAdTags From Library For VideoWaterfallTags
     *
     * @param LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag
     * @param array|VideoWaterfallTagInterface[] $videoWaterfallTags
     * @return mixed
     */
    public function generateVideoDemandAdTagsFromLibraryForVideoWaterfallTags(LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag, array $videoWaterfallTags);
}