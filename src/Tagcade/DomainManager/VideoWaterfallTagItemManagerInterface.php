<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\VideoWaterfallTag;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface VideoWaterfallTagItemManagerInterface extends ManagerInterface
{
    /**
     * get all VideoWaterfallTagItems For a Publisher
     *
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return array|VideoWaterfallTagItemInterface[]
     */
    public function getVideoWaterfallTagItemsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * Get all video ad tag item for ad tag
     * @param VideoWaterfallTag $videoWaterfallTag
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getVideoWaterfallTagItemsForAdTag(VideoWaterfallTag $videoWaterfallTag, $limit = null, $offset = null);
}